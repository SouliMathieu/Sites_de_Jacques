<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class ImageUploadController extends Controller
{
    /**
     * Types de fichiers autorisés
     */
    const ALLOWED_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
    const ALLOWED_VIDEO_MIMES = ['video/mp4', 'video/mov', 'video/avi', 'video/webm'];
    
    const MAX_IMAGE_SIZE = 2048; // 2 Mo
    const MAX_VIDEO_SIZE = 51200; // 50 Mo

    /**
     * Upload de fichiers produits (images et vidéos)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProductImages(Request $request)
    {
        try {
            Log::info('Upload produit - Début', [
                'has_files' => $request->hasFile('files'),
                'user_id' => auth()->id(),
            ]);

            // Déterminer si c'est un upload simple ou multiple
            $isMultiple = $request->hasFile('files') && is_array($request->file('files'));
            
            if ($isMultiple) {
                // Validation pour upload multiple
                $request->validate([
                    'files' => 'required|array|min:1|max:10',
                    'files.*' => [
                        'required',
                        'file',
                        Rule::in(array_merge(self::ALLOWED_IMAGE_MIMES, self::ALLOWED_VIDEO_MIMES)),
                        'max:' . self::MAX_VIDEO_SIZE,
                    ],
                ], [
                    'files.required' => 'Veuillez sélectionner au moins un fichier.',
                    'files.max' => 'Vous ne pouvez pas uploader plus de 10 fichiers à la fois.',
                    'files.*.file' => 'Chaque élément doit être un fichier valide.',
                    'files.*.max' => 'La taille maximale est de 50 Mo par fichier.',
                ]);
                
                $files = $request->file('files');
            } else {
                // Validation pour upload simple
                $request->validate([
                    'files' => [
                        'required',
                        'file',
                        function ($attribute, $value, $fail) {
                            $mimeType = $value->getMimeType();
                            $allowedMimes = array_merge(self::ALLOWED_IMAGE_MIMES, self::ALLOWED_VIDEO_MIMES);
                            
                            if (!in_array($mimeType, $allowedMimes)) {
                                $fail('Le type de fichier n\'est pas autorisé.');
                            }
                        },
                        'max:' . self::MAX_VIDEO_SIZE,
                    ],
                ], [
                    'files.required' => 'Veuillez sélectionner un fichier.',
                    'files.file' => 'Le fichier n\'est pas valide.',
                    'files.max' => 'La taille maximale est de 50 Mo.',
                ]);
                
                $files = [$request->file('files')];
            }

            $uploadedFiles = [];
            $errors = [];

            foreach ($files as $index => $file) {
                try {
                    $result = $this->processFile($file);
                    
                    if ($result['success']) {
                        $uploadedFiles[] = $result['data'];
                    } else {
                        $errors[] = $result['error'];
                    }

                } catch (\Exception $e) {
                    Log::error('Erreur traitement fichier individuel', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);
                    
                    $errors[] = "Erreur sur {$file->getClientOriginalName()}: {$e->getMessage()}";
                }
            }

            // Vérifier si au moins un fichier a été uploadé
            if (empty($uploadedFiles)) {
                Log::warning('Aucun fichier uploadé', ['errors' => $errors]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun fichier n\'a pu être uploadé.',
                    'errors' => $errors,
                ], 400);
            }

            Log::info('Upload terminé avec succès', [
                'uploaded_count' => count($uploadedFiles),
                'errors_count' => count($errors),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => count($uploadedFiles) . ' fichier(s) uploadé(s) avec succès.',
                'errors' => $errors,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur validation upload', [
                'errors' => $e->errors(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erreur générale upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'upload.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Traiter un fichier individuel
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    private function processFile($file)
    {
        try {
            // Vérifier la validité du fichier
            if (!$file->isValid()) {
                return [
                    'success' => false,
                    'error' => 'Fichier invalide: ' . $file->getClientOriginalName(),
                ];
            }

            // Déterminer le type de fichier
            $mimeType = $file->getMimeType();
            $isVideo = in_array($mimeType, self::ALLOWED_VIDEO_MIMES);
            $isImage = in_array($mimeType, self::ALLOWED_IMAGE_MIMES);

            // Vérifier la taille selon le type
            $maxSize = $isVideo ? self::MAX_VIDEO_SIZE : self::MAX_IMAGE_SIZE;
            if ($file->getSize() > $maxSize * 1024) {
                return [
                    'success' => false,
                    'error' => "Fichier trop volumineux: " . $file->getClientOriginalName(),
                ];
            }

            // Générer un nom de fichier unique et sécurisé
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::slug($originalName) . '_' . time() . '_' . uniqid() . '.' . strtolower($extension);

            // Déterminer le dossier de destination
            $folder = $isVideo ? 'products/videos' : 'products/images';

            // Créer le répertoire si nécessaire
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder, 0755, true);
            }

            // Stocker le fichier
            $path = $file->storeAs($folder, $fileName, 'public');

            if (!$path) {
                return [
                    'success' => false,
                    'error' => 'Échec du stockage du fichier',
                ];
            }

            // Optimiser l'image si c'est une image (optionnel - nécessite Intervention Image)
            if ($isImage && class_exists('Intervention\Image\Facades\Image')) {
                try {
                    $this->optimizeImage($path);
                } catch (\Exception $e) {
                    Log::warning('Erreur optimisation image', [
                        'path' => $path,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Fichier traité avec succès', [
                'path' => $path,
                'type' => $isVideo ? 'video' : 'image',
                'size' => $file->getSize(),
                'mime_type' => $mimeType,
            ]);

            return [
                'success' => true,
                'data' => [
                    'url' => Storage::disk('public')->url($path),
                    'path' => $path,
                    'type' => $isVideo ? 'video' : 'image',
                    'name' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $mimeType,
                    'human_size' => $this->formatBytes($file->getSize()),
                ],
            ];

        } catch (\Exception $e) {
            Log::error('Erreur traitement fichier', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Optimiser une image (réduire la qualité/taille)
     *
     * @param string $path
     * @return void
     */
    private function optimizeImage($path)
    {
        try {
            $fullPath = Storage::disk('public')->path($path);
            
            $img = Image::make($fullPath);
            
            // Redimensionner si trop grande (max 1920px de largeur)
            if ($img->width() > 1920) {
                $img->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }
            
            // Sauvegarder avec compression
            $img->save($fullPath, 85);

            Log::info('Image optimisée', ['path' => $path]);

        } catch (\Exception $e) {
            // Ne pas bloquer si l'optimisation échoue
            Log::warning('Échec optimisation image', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Supprimer un fichier
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile(Request $request)
    {
        try {
            $validated = $request->validate([
                'file_path' => 'required|string|max:500',
            ], [
                'file_path.required' => 'Le chemin du fichier est obligatoire.',
            ]);

            $filePath = $validated['file_path'];

            Log::info('Demande suppression fichier', [
                'file_path' => $filePath,
                'user_id' => auth()->id(),
            ]);

            // Nettoyer le chemin
            $cleanPath = ltrim($filePath, '/');
            $cleanPath = str_replace('storage/', '', $cleanPath);

            // Sécurité : vérifier que le chemin est dans products/
            if (!str_starts_with($cleanPath, 'products/')) {
                Log::warning('Tentative suppression fichier hors products/', [
                    'path' => $cleanPath,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Chemin de fichier non autorisé.',
                ], 403);
            }

            // Vérifier l'existence et supprimer
            if (Storage::disk('public')->exists($cleanPath)) {
                Storage::disk('public')->delete($cleanPath);

                Log::info('Fichier supprimé avec succès', [
                    'path' => $cleanPath,
                    'user_id' => auth()->id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Fichier supprimé avec succès.',
                ]);
            }

            Log::warning('Fichier non trouvé pour suppression', [
                'path' => $cleanPath,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fichier introuvable.',
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Erreur suppression fichier', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_path' => $request->input('file_path'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Obtenir les informations d'un fichier
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileInfo(Request $request)
    {
        try {
            $validated = $request->validate([
                'file_path' => 'required|string',
            ]);

            $filePath = ltrim($validated['file_path'], '/');
            $filePath = str_replace('storage/', '', $filePath);

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable.',
                ], 404);
            }

            $fullPath = Storage::disk('public')->path($filePath);
            $mimeType = Storage::disk('public')->mimeType($filePath);
            $size = Storage::disk('public')->size($filePath);

            return response()->json([
                'success' => true,
                'data' => [
                    'path' => $filePath,
                    'url' => Storage::disk('public')->url($filePath),
                    'size' => $size,
                    'human_size' => $this->formatBytes($size),
                    'mime_type' => $mimeType,
                    'type' => str_starts_with($mimeType, 'video/') ? 'video' : 'image',
                    'last_modified' => Storage::disk('public')->lastModified($filePath),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des informations.',
            ], 500);
        }
    }

    /**
     * Formater la taille en octets en format lisible
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Nettoyer les fichiers orphelins (optionnel)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function cleanOrphanFiles()
    {
        try {
            // TODO: Implémenter la logique pour nettoyer les fichiers non référencés
            // 1. Récupérer tous les chemins de fichiers dans les produits
            // 2. Lister tous les fichiers dans storage/app/public/products/
            // 3. Supprimer ceux qui ne sont pas référencés

            return response()->json([
                'success' => true,
                'message' => 'Nettoyage terminé.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du nettoyage.',
            ], 500);
        }
    }
}

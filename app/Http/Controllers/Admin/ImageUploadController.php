<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImageUploadController extends Controller
{
    public function uploadProductImages(Request $request)
    {
        try {
            Log::info('Upload request received', [
                'has_files' => $request->hasFile('files'),
                'request_data' => $request->all(),
                'files_info' => $request->file('files') ? get_class($request->file('files')) : 'null'
            ]);

            // CORRECTION: Validation pour fichier unique ou multiple
            if ($request->hasFile('files') && is_array($request->file('files'))) {
                // Upload multiple (si vous changez SimpleDropzone plus tard)
                $request->validate([
                    'files' => 'required|array|min:1',
                    'files.*' => 'required|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/mov,video/avi|max:20480'
                ]);
                $files = $request->file('files');
            } else {
                // Upload simple (SimpleDropzone actuel)
                $request->validate([
                    'files' => 'required|file|mimetypes:image/jpeg,image/png,image/gif,image/webp,video/mp4,video/mov,video/avi|max:20480'
                ]);
                $files = [$request->file('files')]; // Convertir en array pour traitement uniforme
            }

            $uploadedFiles = [];

            foreach ($files as $file) {
                try {
                    // Vérifier que le fichier est valide
                    if (!$file->isValid()) {
                        Log::error('Invalid file uploaded', ['file' => $file->getClientOriginalName()]);
                        continue;
                    }

                    // Générer un nom de fichier unique
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $fileName = Str::slug($originalName) . '_' . time() . '_' . uniqid() . '.' . $extension;

                    // Déterminer le type de fichier
                    $mimeType = $file->getMimeType();
                    $isVideo = str_starts_with($mimeType, 'video/');

                    // CORRECTION: Dossiers cohérents
                    $folder = $isVideo ? 'products/videos' : 'products/images';

                    // Créer le répertoire s'il n'existe pas
                    $fullPath = storage_path('app/public/' . $folder);
                    if (!file_exists($fullPath)) {
                        mkdir($fullPath, 0755, true);
                    }

                    // Stocker le fichier
                    $path = $file->storeAs($folder, $fileName, 'public');

                    if ($path) {
                        $uploadedFiles[] = [
                            'url' => $path, // CORRECTION: Retourner le chemin relatif, pas l'URL complète
                            'type' => $isVideo ? 'video' : 'image',
                            'name' => $fileName,
                            'original_name' => $file->getClientOriginalName(),
                            'size' => $file->getSize()
                        ];

                        Log::info('File uploaded successfully', [
                            'path' => $path,
                            'type' => $isVideo ? 'video' : 'image',
                            'size' => $file->getSize()
                        ]);
                    } else {
                        Log::error('Failed to store file', ['file' => $fileName]);
                    }

                } catch (\Exception $e) {
                    Log::error('Error uploading individual file', [
                        'file' => $file->getClientOriginalName(),
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if (empty($uploadedFiles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun fichier n\'a pu être uploadé'
                ], 400);
            }

            Log::info('Upload completed successfully', [
                'uploaded_files_count' => count($uploadedFiles),
                'files' => $uploadedFiles
            ]);

            return response()->json([
                'success' => true,
                'files' => $uploadedFiles,
                'message' => count($uploadedFiles) . ' fichier(s) uploadé(s) avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error during upload', ['errors' => $e->errors()]);

            $errorMessage = 'Erreur de validation: ';
            if (isset($e->errors()['files'])) {
                $errorMessage .= implode(', ', $e->errors()['files']);
            } elseif (isset($e->errors()['files.0'])) {
                $errorMessage .= implode(', ', $e->errors()['files.0']);
            } else {
                $errorMessage .= 'Fichier invalide';
            }

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('General error during upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteFile(Request $request)
    {
        try {
            $request->validate([
                'file_path' => 'required|string'
            ]);

            $filePath = $request->input('file_path');

            Log::info('Delete request received', ['file_path' => $filePath]);

            // CORRECTION: Traitement du chemin simplifié
            // Le frontend envoie déjà le bon chemin relatif (ex: "products/images/file.jpg")
            $realPath = $filePath;

            // Nettoyer le chemin au cas où il y aurait des slashes en trop
            $realPath = ltrim($realPath, '/');

            Log::info('Attempting to delete file', ['real_path' => $realPath]);

            if (Storage::disk('public')->exists($realPath)) {
                Storage::disk('public')->delete($realPath);

                Log::info('File deleted successfully', ['path' => $realPath]);

                return response()->json([
                    'success' => true,
                    'message' => 'Fichier supprimé avec succès'
                ]);
            }

            Log::warning('File not found for deletion', [
                'path' => $realPath,
                'storage_path' => storage_path('app/public/' . $realPath),
                'file_exists' => file_exists(storage_path('app/public/' . $realPath))
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fichier non trouvé: ' . $realPath
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error during file deletion', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file_path' => $request->input('file_path')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }
}

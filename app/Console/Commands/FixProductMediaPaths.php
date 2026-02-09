<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixProductMediaPaths extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fix-media-paths {--dry-run : Afficher les corrections sans les appliquer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corriger les chemins des images et vidéos des produits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 MODE DIAGNOSTIC (DRY RUN) - Aucune modification ne sera effectuée');
            $this->newLine();
        } else {
            $this->info('🔧 MODE CORRECTION - Les fichiers seront modifiés');
            $this->newLine();
        }

        $products = Product::all();
        $this->info("📦 Analyse de {$products->count()} produits...");
        $this->newLine();

        $stats = [
            'images_fixed' => 0,
            'videos_fixed' => 0,
            'images_errors' => 0,
            'videos_errors' => 0,
        ];

        foreach ($products as $product) {
            $this->line("Produit #{$product->id}: {$product->name}");

            // ========================================
            // CORRECTION DES IMAGES
            // ========================================
            if ($product->images && count($product->images) > 0) {
                $correctedImages = [];
                $hasChanges = false;

                foreach ($product->images as $index => $imagePath) {
                    $this->line("  📷 Image {$index}: {$imagePath}");

                    // Nettoyer le chemin (supprimer les préfixes redondants)
                    $cleanPath = $this->cleanPath($imagePath);
                    
                    // Tester différents emplacements possibles
                    $possiblePaths = $this->getPossibleImagePaths($cleanPath);
                    
                    $found = false;
                    $correctPath = null;

                    foreach ($possiblePaths as $testPath) {
                        if (Storage::disk('public')->exists($testPath)) {
                            $found = true;
                            $correctPath = str_replace('products/images/', '', $testPath);
                            
                            if ($correctPath !== $imagePath) {
                                $hasChanges = true;
                                $this->info("    ✅ Trouvé à: {$testPath}");
                                $this->warn("    🔄 Correction: {$imagePath} → {$correctPath}");
                                $stats['images_fixed']++;
                            } else {
                                $this->info("    ✅ Chemin déjà correct");
                            }
                            break;
                        }
                    }

                    if (!$found) {
                        $this->error("    ❌ Fichier introuvable dans:");
                        foreach ($possiblePaths as $path) {
                            $this->error("       - storage/app/public/{$path}");
                        }
                        $stats['images_errors']++;
                        $correctedImages[] = $imagePath; // Garder l'ancien chemin
                    } else {
                        $correctedImages[] = $correctPath;
                    }
                }

                // Appliquer les corrections
                if ($hasChanges && !$dryRun) {
                    $product->update(['images' => $correctedImages]);
                    $this->info("  💾 Images mises à jour en base de données");
                }
            }

            // ========================================
            // CORRECTION DES VIDÉOS
            // ========================================
            if ($product->videos && count($product->videos) > 0) {
                $correctedVideos = [];
                $hasChanges = false;

                foreach ($product->videos as $index => $videoPath) {
                    $this->line("  🎥 Vidéo {$index}: {$videoPath}");

                    // Nettoyer le chemin
                    $cleanPath = $this->cleanPath($videoPath);
                    
                    // Tester différents emplacements possibles
                    $possiblePaths = $this->getPossibleVideoPaths($cleanPath);
                    
                    $found = false;
                    $correctPath = null;

                    foreach ($possiblePaths as $testPath) {
                        if (Storage::disk('public')->exists($testPath)) {
                            $found = true;
                            $correctPath = str_replace('products/videos/', '', $testPath);
                            
                            if ($correctPath !== $videoPath) {
                                $hasChanges = true;
                                $this->info("    ✅ Trouvé à: {$testPath}");
                                $this->warn("    🔄 Correction: {$videoPath} → {$correctPath}");
                                $stats['videos_fixed']++;
                            } else {
                                $this->info("    ✅ Chemin déjà correct");
                            }
                            break;
                        }
                    }

                    if (!$found) {
                        $this->error("    ❌ Fichier introuvable dans:");
                        foreach ($possiblePaths as $path) {
                            $this->error("       - storage/app/public/{$path}");
                        }
                        $stats['videos_errors']++;
                        $correctedVideos[] = $videoPath; // Garder l'ancien chemin
                    } else {
                        $correctedVideos[] = $correctPath;
                    }
                }

                // Appliquer les corrections
                if ($hasChanges && !$dryRun) {
                    $product->update(['videos' => $correctedVideos]);
                    $this->info("  💾 Vidéos mises à jour en base de données");
                }
            }

            $this->newLine();
        }

        // ========================================
        // RAPPORT FINAL
        // ========================================
        $this->newLine();
        $this->info('📊 RAPPORT FINAL');
        $this->table(
            ['Type', 'Statut', 'Nombre'],
            [
                ['Images', 'Corrigées', $stats['images_fixed']],
                ['Images', 'Erreurs', $stats['images_errors']],
                ['Vidéos', 'Corrigées', $stats['videos_fixed']],
                ['Vidéos', 'Erreurs', $stats['videos_errors']],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('⚠️  MODE DIAGNOSTIC - Aucune modification appliquée');
            $this->info('Pour appliquer les corrections, exécutez: php artisan products:fix-media-paths');
        } else {
            $this->newLine();
            $this->info('✅ Corrections appliquées avec succès !');
        }

        return Command::SUCCESS;
    }

    /**
     * Nettoyer un chemin de fichier
     */
    private function cleanPath(string $path): string
    {
        // Supprimer les préfixes courants
        $path = str_replace(['products/images/', 'products/videos/', 'images/', 'videos/'], '', $path);
        
        // Supprimer les slashes multiples
        $path = preg_replace('#/+#', '/', $path);
        
        return trim($path, '/');
    }

    /**
     * Obtenir les chemins possibles pour une image
     */
    private function getPossibleImagePaths(string $filename): array
    {
        return [
            "products/images/{$filename}",
            "products/images/products/images/{$filename}",
            "images/{$filename}",
            $filename,
        ];
    }

    /**
     * Obtenir les chemins possibles pour une vidéo
     */
    private function getPossibleVideoPaths(string $filename): array
    {
        return [
            "products/videos/{$filename}",
            "products/videos/products/videos/{$filename}",
            "videos/{$filename}",
            $filename,
        ];
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class DiagnoseMediaStorage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'products:diagnose-media';

    /**
     * The console command description.
     */
    protected $description = 'Diagnostiquer l\'emplacement des fichiers médias (images et vidéos)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 DIAGNOSTIC DU STOCKAGE DES MÉDIAS');
        $this->newLine();

        // ========================================
        // 1. VÉRIFIER LA CONFIGURATION
        // ========================================
        $this->info('📋 Configuration du stockage:');
        $publicPath = Storage::disk('public')->path('');
        $this->line("   Disk 'public': {$publicPath}");
        $this->newLine();

        // ========================================
        // 2. SCANNER LE RÉPERTOIRE PUBLIC
        // ========================================
        $this->info('📁 Fichiers dans storage/app/public/:');
        $this->scanDirectory('', 0);
        $this->newLine();

        // ========================================
        // 3. SCANNER LE RÉPERTOIRE PUBLIC/PRODUCTS
        // ========================================
        if (File::exists(public_path('products'))) {
            $this->info('📁 Fichiers dans public/products/ (ancien système):');
            $this->scanPublicDirectory('products', 0);
            $this->newLine();
        }

        // ========================================
        // 4. STATISTIQUES
        // ========================================
        $this->info('📊 STATISTIQUES:');
        
        $imagesInStorage = collect(Storage::disk('public')->allFiles('products/images'))->count();
        $videosInStorage = collect(Storage::disk('public')->allFiles('products/videos'))->count();
        
        $this->table(
            ['Emplacement', 'Images', 'Vidéos'],
            [
                ['storage/app/public/products/', $imagesInStorage, $videosInStorage],
            ]
        );

        $this->newLine();
        $this->info('✅ Diagnostic terminé');
        
        return Command::SUCCESS;
    }

    /**
     * Scanner un répertoire dans storage/app/public
     */
    private function scanDirectory(string $path, int $depth = 0)
    {
        if ($depth > 3) return; // Limiter la profondeur

        $directories = Storage::disk('public')->directories($path);
        $files = Storage::disk('public')->files($path);

        $indent = str_repeat('  ', $depth);

        foreach ($directories as $dir) {
            $dirName = basename($dir);
            $fileCount = count(Storage::disk('public')->allFiles($dir));
            $this->line("{$indent}📂 {$dirName}/ ({$fileCount} fichiers)");
            $this->scanDirectory($dir, $depth + 1);
        }

        if ($depth <= 2) {
            foreach ($files as $file) {
                $fileName = basename($file);
                $size = Storage::disk('public')->size($file);
                $sizeKb = round($size / 1024, 2);
                $this->line("{$indent}   📄 {$fileName} ({$sizeKb} KB)");
            }
        }
    }

    /**
     * Scanner le répertoire public/ (ancien système)
     */
    private function scanPublicDirectory(string $path, int $depth = 0)
    {
        if ($depth > 3) return;

        $fullPath = public_path($path);
        if (!File::exists($fullPath)) return;

        $directories = File::directories($fullPath);
        $files = File::files($fullPath);

        $indent = str_repeat('  ', $depth);

        foreach ($directories as $dir) {
            $dirName = basename($dir);
            $fileCount = count(File::allFiles($dir));
            $this->line("{$indent}📂 {$dirName}/ ({$fileCount} fichiers)");
            $this->scanPublicDirectory($path . '/' . $dirName, $depth + 1);
        }

        if ($depth <= 2) {
            foreach ($files as $file) {
                $fileName = $file->getFilename();
                $size = $file->getSize();
                $sizeKb = round($size / 1024, 2);
                $this->line("{$indent}   📄 {$fileName} ({$sizeKb} KB)");
            }
        }
    }
}
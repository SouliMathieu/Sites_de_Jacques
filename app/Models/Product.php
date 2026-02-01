<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'specifications', 'price', 'promotional_price',
        'stock_quantity', 'warranty', 'category_id', 'images', 'videos',
        'is_featured', 'is_active', 'meta_title', 'meta_description'
    ];

    protected $casts = [
        'images' => 'array',
        'videos' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function getCurrentPriceAttribute()
    {
        return $this->promotional_price ?? $this->price;
    }

    // ✅ ACCESSEUR pour la première image - Images dans products/images/
    public function getFirstImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            $firstImagePath = $this->images[0];
            // ✅ CHEMIN CORRECT : Images dans products/images/
            if (file_exists(storage_path('app/public/products/images/' . $firstImagePath))) {
                return asset('storage/products/images/' . $firstImagePath);
            }
        }
        return asset('images/placeholder-product.jpg');
    }

    // ✅ ACCESSEUR pour la première vidéo - CHEMINS CORRIGÉS pour products/videos/
    public function getFirstVideoAttribute()
    {
        if ($this->videos && count($this->videos) > 0) {
            $firstVideoPath = $this->videos[0];
            
            // Nettoyer le chemin (enlever products/ et videos/ s'ils sont déjà présents)
            $cleanPath = str_replace('products/', '', $firstVideoPath);
            $cleanPath = str_replace('videos/', '', $cleanPath);
            
            // ✅ CHEMIN CORRECT : Vidéos dans products/videos/
            if (file_exists(storage_path('app/public/products/videos/' . $cleanPath))) {
                return asset('storage/products/videos/' . $cleanPath);
            }
        }
        return null;
    }

    // ✅ VÉRIFIEUR de présence de vidéos
    public function getHasVideosAttribute()
    {
        return $this->videos && count($this->videos) > 0;
    }

    // ✅ TOUTES LES IMAGES valides - Images dans products/images/
    public function getAllImagesAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return array_filter($this->images, function($imagePath) {
                // ✅ CHEMIN CORRECT : Images dans products/images/
                return file_exists(storage_path('app/public/products/images/' . $imagePath));
            });
        }
        return [];
    }

    // ✅ TOUTES LES VIDÉOS valides - CHEMINS CORRIGÉS pour products/videos/
    public function getAllVideosAttribute()
    {
        if ($this->videos && count($this->videos) > 0) {
            return array_filter($this->videos, function($videoPath) {
                // Nettoyer le chemin
                $cleanPath = str_replace('products/', '', $videoPath);
                $cleanPath = str_replace('videos/', '', $cleanPath);
                
                // ✅ CHEMIN CORRECT : Vidéos dans products/videos/
                return file_exists(storage_path('app/public/products/videos/' . $cleanPath));
            });
        }
        return [];
    }

    // ✅ URLs complètes pour toutes les images - Images dans products/images/
    public function getImageUrlsAttribute()
    {
        return collect($this->all_images)->map(function($imagePath) {
            // ✅ CHEMIN CORRECT : Images dans products/images/
            return asset('storage/products/images/' . $imagePath);
        })->toArray();
    }

    // ✅ URLs complètes pour toutes les vidéos - CHEMINS CORRIGÉS pour products/videos/
    public function getVideoUrlsAttribute()
    {
        return collect($this->all_videos)->map(function($videoPath) {
            // Nettoyer le chemin
            $cleanPath = str_replace('products/', '', $videoPath);
            $cleanPath = str_replace('videos/', '', $cleanPath);
            
            // ✅ CHEMIN CORRECT : Vidéos dans products/videos/
            return asset('storage/products/videos/' . $cleanPath);
        })->toArray();
    }

    // ✅ NOUVELLE MÉTHODE : Ajouter une image
    public function addImage($imagePath)
    {
        $images = $this->images ?: [];
        $images[] = $imagePath;
        $this->update(['images' => $images]);
    }

    // ✅ NOUVELLE MÉTHODE : Ajouter une vidéo
    public function addVideo($videoPath)
    {
        $videos = $this->videos ?: [];
        $videos[] = $videoPath;
        $this->update(['videos' => $videos]);
    }

    // ✅ NOUVELLE MÉTHODE : Supprimer une image - Images dans products/images/
    public function removeImage($imagePath)
    {
        if ($this->images) {
            $images = array_filter($this->images, function($path) use ($imagePath) {
                return $path !== $imagePath;
            });
            $this->update(['images' => array_values($images)]);

            // ✅ CHEMIN CORRECT : Images dans products/images/
            $fullPath = storage_path('app/public/products/images/' . $imagePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    // ✅ NOUVELLE MÉTHODE : Supprimer une vidéo - CHEMINS CORRIGÉS pour products/videos/
    public function removeVideo($videoPath)
    {
        if ($this->videos) {
            $videos = array_filter($this->videos, function($path) use ($videoPath) {
                return $path !== $videoPath;
            });
            $this->update(['videos' => array_values($videos)]);

            // Nettoyer le chemin
            $cleanPath = str_replace('products/', '', $videoPath);
            $cleanPath = str_replace('videos/', '', $cleanPath);

            // ✅ CHEMIN CORRECT : Vidéos dans products/videos/
            $fullPath = storage_path('app/public/products/videos/' . $cleanPath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }

    // ✅ NOUVELLE MÉTHODE : Remplacer toutes les images - Images dans products/images/
    public function setImages(array $imagePaths)
    {
        // Supprimer les anciennes images
        if ($this->images) {
            foreach ($this->images as $oldImage) {
                // ✅ CHEMIN CORRECT : Images dans products/images/
                $fullPath = storage_path('app/public/products/images/' . $oldImage);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $this->update(['images' => $imagePaths]);
    }

    // ✅ NOUVELLE MÉTHODE : Remplacer toutes les vidéos - CHEMINS CORRIGÉS pour products/videos/
    public function setVideos(array $videoPaths)
    {
        // Supprimer les anciennes vidéos
        if ($this->videos) {
            foreach ($this->videos as $oldVideo) {
                // Nettoyer le chemin
                $cleanPath = str_replace('products/', '', $oldVideo);
                $cleanPath = str_replace('videos/', '', $cleanPath);

                // ✅ CHEMIN CORRECT : Vidéos dans products/videos/
                $fullPath = storage_path('app/public/products/videos/' . $cleanPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        $this->update(['videos' => $videoPaths]);
    }

    // ✅ NOUVELLE MÉTHODE : Compter les images
    public function getImagesCountAttribute()
    {
        return count($this->all_images);
    }

    // ✅ NOUVELLE MÉTHODE : Compter les vidéos
    public function getVideosCountAttribute()
    {
        return count($this->all_videos);
    }

    // ✅ MÉTHODE : Vérifier si le produit a au moins une image
    public function hasImages()
    {
        return $this->images_count > 0;
    }

    // ✅ MÉTHODE : Vérifier si le produit a au moins une vidéo
    public function hasVideos()
    {
        return $this->videos_count > 0;
    }

    // ✅ SCOPE : Produits avec images
    public function scopeWithImages($query)
    {
        return $query->whereNotNull('images')
                    ->where('images', '!=', '[]');
    }

    // ✅ SCOPE : Produits avec vidéos
    public function scopeWithVideos($query)
    {
        return $query->whereNotNull('videos')
                    ->where('videos', '!=', '[]');
    }

    // ✅ NOUVELLE MÉTHODE : Debug des chemins (à supprimer en production)
    public function debugPaths()
    {
        if (!config('app.debug')) return null;

        $debug = [];
        
        // Debug images
        if ($this->images && count($this->images) > 0) {
            $firstImage = $this->images[0];
            $debug['first_image_name'] = $firstImage;
            $debug['image_storage_path'] = storage_path('app/public/products/images/' . $firstImage);
            $debug['image_file_exists'] = file_exists(storage_path('app/public/products/images/' . $firstImage));
            $debug['image_asset_url'] = asset('storage/products/images/' . $firstImage);
        }
        
        // Debug vidéos
        if ($this->videos && count($this->videos) > 0) {
            $firstVideo = $this->videos[0];
            $cleanVideoPath = str_replace(['products/', 'videos/'], '', $firstVideo);
            $debug['first_video_name'] = $firstVideo;
            $debug['video_clean_path'] = $cleanVideoPath;
            $debug['video_storage_path'] = storage_path('app/public/products/videos/' . $cleanVideoPath);
            $debug['video_file_exists'] = file_exists(storage_path('app/public/products/videos/' . $cleanVideoPath));
            $debug['video_asset_url'] = asset('storage/products/videos/' . $cleanVideoPath);
        }
        
        return $debug;
    }
}

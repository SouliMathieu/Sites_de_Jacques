<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs assignables en masse
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'specifications',
        'price',
        'promotional_price',
        'stock_quantity',
        'warranty',
        'category_id',
        'images',
        'videos',
        'is_featured',
        'is_active',
        'sort_order',
        'views_count',
        'sales_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * Les attributs qui doivent être castés
     *
     * @var array<string, string>
     */
    protected $casts = [
        'images' => 'array',
        'videos' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'promotional_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Les attributs ajoutés dynamiquement
     *
     * @var array<int, string>
     */
    protected $appends = [
        'current_price',
        'formatted_price',
        'formatted_promotional_price',
        'discount_percentage',
        'first_image',
        'first_video',
        'image_urls',
        'video_urls',
        'images_count',
        'videos_count',
        'is_in_stock',
        'is_on_sale',
        'stock_status',
    ];

    /**
     * Les attributs cachés
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Configuration des valeurs par défaut
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'stock_quantity' => 0,
        'views_count' => 0,
        'sales_count' => 0,
        'sort_order' => 0,
    ];

    /**
     * Boot du modèle
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Générer automatiquement le slug lors de la création
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            // Générer un slug unique
            $originalSlug = $product->slug;
            $count = 1;

            while (static::where('slug', $product->slug)->exists()) {
                $product->slug = $originalSlug . '-' . $count;
                $count++;
            }
        });

        // Nettoyer les fichiers lors de la suppression
        static::deleting(function ($product) {
            $product->deleteAllMedia();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Catégorie du produit
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Articles de commandes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Avis clients (si vous avez cette fonctionnalité)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (GETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le prix actuel (avec ou sans promotion)
     *
     * @return float
     */
    public function getCurrentPriceAttribute()
    {
        return $this->promotional_price ?? $this->price;
    }

    /**
     * Obtenir le prix formaté
     *
     * @return string
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir le prix promotionnel formaté
     *
     * @return string|null
     */
    public function getFormattedPromotionalPriceAttribute()
    {
        if ($this->promotional_price) {
            return number_format($this->promotional_price, 0, ',', ' ') . ' FCFA';
        }
        return null;
    }

    /**
     * Calculer le pourcentage de réduction
     *
     * @return int|null
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->promotional_price && $this->price > 0) {
            return round((($this->price - $this->promotional_price) / $this->price) * 100);
        }
        return null;
    }

    /**
     * Vérifier si le produit est en stock
     *
     * @return bool
     */
    public function getIsInStockAttribute()
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Vérifier si le produit est en promotion
     *
     * @return bool
     */
    public function getIsOnSaleAttribute()
    {
        return $this->promotional_price !== null && $this->promotional_price < $this->price;
    }

    /**
     * Obtenir le statut du stock
     *
     * @return string
     */
    public function getStockStatusAttribute()
    {
        if ($this->stock_quantity > 10) {
            return 'En stock';
        } elseif ($this->stock_quantity > 0) {
            return 'Stock limité';
        } else {
            return 'Rupture de stock';
        }
    }

    /**
     * Obtenir la première image
     *
     * @return string
     */
    public function getFirstImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            $firstImagePath = $this->images[0];
            $storagePath = "products/images/{$firstImagePath}";
            
            if (Storage::disk('public')->exists($storagePath)) {
                return Storage::disk('public')->url($storagePath);
            }
        }
        
        return asset('images/placeholder-product.jpg');
    }

    /**
     * Obtenir la première vidéo
     *
     * @return string|null
     */
    public function getFirstVideoAttribute()
    {
        if ($this->videos && count($this->videos) > 0) {
            $firstVideoPath = $this->videos[0];
            $cleanPath = $this->cleanMediaPath($firstVideoPath);
            $storagePath = "products/videos/{$cleanPath}";
            
            if (Storage::disk('public')->exists($storagePath)) {
                return Storage::disk('public')->url($storagePath);
            }
        }
        
        return null;
    }

    /**
     * Vérifier la présence de vidéos
     *
     * @return bool
     */
    public function getHasVideosAttribute()
    {
        return $this->videos && count($this->videos) > 0;
    }

    /**
     * Obtenir toutes les images valides
     *
     * @return array
     */
    public function getAllImagesAttribute()
    {
        if (!$this->images || count($this->images) === 0) {
            return [];
        }

        return array_filter($this->images, function ($imagePath) {
            $storagePath = "products/images/{$imagePath}";
            return Storage::disk('public')->exists($storagePath);
        });
    }

    /**
     * Obtenir toutes les vidéos valides
     *
     * @return array
     */
    public function getAllVideosAttribute()
    {
        if (!$this->videos || count($this->videos) === 0) {
            return [];
        }

        return array_filter($this->videos, function ($videoPath) {
            $cleanPath = $this->cleanMediaPath($videoPath);
            $storagePath = "products/videos/{$cleanPath}";
            return Storage::disk('public')->exists($storagePath);
        });
    }

    /**
     * URLs complètes pour toutes les images
     *
     * @return array
     */
    public function getImageUrlsAttribute()
    {
        return collect($this->all_images)->map(function ($imagePath) {
            return Storage::disk('public')->url("products/images/{$imagePath}");
        })->toArray();
    }

    /**
     * URLs complètes pour toutes les vidéos
     *
     * @return array
     */
    public function getVideoUrlsAttribute()
    {
        return collect($this->all_videos)->map(function ($videoPath) {
            $cleanPath = $this->cleanMediaPath($videoPath);
            return Storage::disk('public')->url("products/videos/{$cleanPath}");
        })->toArray();
    }

    /**
     * Compter les images
     *
     * @return int
     */
    public function getImagesCountAttribute()
    {
        return count($this->all_images);
    }

    /**
     * Compter les vidéos
     *
     * @return int
     */
    public function getVideosCountAttribute()
    {
        return count($this->all_videos);
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS (SETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Définir le nom et générer le slug
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(trim($value));
        
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Définir le slug avec nettoyage
     *
     * @param string $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Définir le prix avec validation
     *
     * @param mixed $value
     * @return void
     */
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = max(0, (float) $value);
    }

    /**
     * Définir le prix promotionnel avec validation
     *
     * @param mixed $value
     * @return void
     */
    public function setPromotionalPriceAttribute($value)
    {
        $this->attributes['promotional_price'] = $value ? max(0, (float) $value) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope produits actifs
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope produits en stock
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInStock(Builder $query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope produits vedettes
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope produits en promotion
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnSale(Builder $query)
    {
        return $query->whereNotNull('promotional_price')
            ->whereColumn('promotional_price', '<', 'price');
    }

    /**
     * Scope avec images
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithImages(Builder $query)
    {
        return $query->whereNotNull('images')
            ->where('images', '!=', '[]');
    }

    /**
     * Scope avec vidéos
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithVideos(Builder $query)
    {
        return $query->whereNotNull('videos')
            ->where('videos', '!=', '[]');
    }

    /**
     * Scope recherche
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('specifications', 'like', "%{$search}%")
              ->orWhereHas('category', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope triés
     *
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrdered(Builder $query, $direction = 'asc')
    {
        return $query->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', $direction)
            ->orderBy('name', $direction);
    }

    /**
     * Scope par catégorie
     *
     * @param Builder $query
     * @param int $categoryId
     * @return Builder
     */
    public function scopeByCategory(Builder $query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope par plage de prix
     *
     * @param Builder $query
     * @param float $min
     * @param float $max
     * @return Builder
     */
    public function scopePriceRange(Builder $query, $min, $max)
    {
        return $query->whereRaw('COALESCE(promotional_price, price) BETWEEN ? AND ?', [$min, $max]);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES DE GESTION DES MÉDIAS
    |--------------------------------------------------------------------------
    */

    /**
     * Ajouter une image
     *
     * @param string $imagePath
     * @return bool
     */
    public function addImage($imagePath)
    {
        $images = $this->images ?: [];
        $images[] = $imagePath;
        return $this->update(['images' => $images]);
    }

    /**
     * Ajouter une vidéo
     *
     * @param string $videoPath
     * @return bool
     */
    public function addVideo($videoPath)
    {
        $videos = $this->videos ?: [];
        $videos[] = $videoPath;
        return $this->update(['videos' => $videos]);
    }

    /**
     * Supprimer une image
     *
     * @param string $imagePath
     * @return bool
     */
    public function removeImage($imagePath)
    {
        if ($this->images) {
            $images = array_filter($this->images, fn($path) => $path !== $imagePath);
            $this->update(['images' => array_values($images)]);

            $storagePath = "products/images/{$imagePath}";
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            return true;
        }

        return false;
    }

    /**
     * Supprimer une vidéo
     *
     * @param string $videoPath
     * @return bool
     */
    public function removeVideo($videoPath)
    {
        if ($this->videos) {
            $videos = array_filter($this->videos, fn($path) => $path !== $videoPath);
            $this->update(['videos' => array_values($videos)]);

            $cleanPath = $this->cleanMediaPath($videoPath);
            $storagePath = "products/videos/{$cleanPath}";
            
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }

            return true;
        }

        return false;
    }

    /**
     * Remplacer toutes les images
     *
     * @param array $imagePaths
     * @return bool
     */
    public function setImages(array $imagePaths)
    {
        // Supprimer les anciennes
        if ($this->images) {
            foreach ($this->images as $oldImage) {
                $storagePath = "products/images/{$oldImage}";
                if (Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }
        }

        return $this->update(['images' => $imagePaths]);
    }

    /**
     * Remplacer toutes les vidéos
     *
     * @param array $videoPaths
     * @return bool
     */
    public function setVideos(array $videoPaths)
    {
        // Supprimer les anciennes
        if ($this->videos) {
            foreach ($this->videos as $oldVideo) {
                $cleanPath = $this->cleanMediaPath($oldVideo);
                $storagePath = "products/videos/{$cleanPath}";
                
                if (Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }
        }

        return $this->update(['videos' => $videoPaths]);
    }

    /**
     * Supprimer tous les médias
     *
     * @return void
     */
    public function deleteAllMedia()
    {
        // Supprimer images
        if ($this->images) {
            foreach ($this->images as $image) {
                $storagePath = "products/images/{$image}";
                if (Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }
        }

        // Supprimer vidéos
        if ($this->videos) {
            foreach ($this->videos as $video) {
                $cleanPath = $this->cleanMediaPath($video);
                $storagePath = "products/videos/{$cleanPath}";
                
                if (Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }
        }
    }

    /**
     * Nettoyer le chemin d'un média
     *
     * @param string $path
     * @return string
     */
    private function cleanMediaPath($path)
    {
        return str_replace(['products/', 'videos/', 'images/'], '', $path);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifier si le produit a des images
     *
     * @return bool
     */
    public function hasImages()
    {
        return $this->images_count > 0;
    }

    /**
     * Vérifier si le produit a des vidéos
     *
     * @return bool
     */
    public function hasVideos()
    {
        return $this->videos_count > 0;
    }

    /**
     * Activer le produit
     *
     * @return bool
     */
    public function activate()
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Désactiver le produit
     *
     * @return bool
     */
    public function deactivate()
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Marquer comme vedette
     *
     * @return bool
     */
    public function markAsFeatured()
    {
        return $this->update(['is_featured' => true]);
    }

    /**
     * Retirer de vedette
     *
     * @return bool
     */
    public function unmarkAsFeatured()
    {
        return $this->update(['is_featured' => false]);
    }

    /**
     * Incrémenter les vues
     *
     * @return bool
     */
    public function incrementViews()
    {
        return $this->increment('views_count');
    }

    /**
     * Incrémenter les ventes
     *
     * @param int $quantity
     * @return bool
     */
    public function incrementSales($quantity = 1)
    {
        return $this->increment('sales_count', $quantity);
    }

    /**
     * Décrémenter le stock
     *
     * @param int $quantity
     * @return bool
     */
    public function decrementStock($quantity)
    {
        if ($this->stock_quantity >= $quantity) {
            return $this->decrement('stock_quantity', $quantity);
        }
        
        return false;
    }

    /**
     * Incrémenter le stock
     *
     * @param int $quantity
     * @return bool
     */
    public function incrementStock($quantity)
    {
        return $this->increment('stock_quantity', $quantity);
    }

    /**
     * Dupliquer le produit
     *
     * @return static
     */
    public function duplicate()
    {
        $newProduct = $this->replicate();
        $newProduct->name = $this->name . ' (Copie)';
        $newProduct->slug = Str::slug($newProduct->name);
        $newProduct->is_active = false;
        $newProduct->save();

        return $newProduct;
    }
}

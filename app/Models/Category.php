<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Category extends Model
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
        'image',
        'icon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
        'is_featured',
        'sort_order',
        'parent_id',
    ];

    /**
     * Les attributs qui doivent être castés
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
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
        'image_url',
        'full_url',
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
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // Générer un slug unique si nécessaire
            $originalSlug = $category->slug;
            $count = 1;

            while (static::where('slug', $category->slug)->exists()) {
                $category->slug = $originalSlug . '-' . $count;
                $count++;
            }

            // Meta title/description par défaut
            if (empty($category->meta_title)) {
                $category->meta_title = $category->name;
            }

            if (empty($category->meta_description) && $category->description) {
                $category->meta_description = Str::limit(strip_tags($category->description), 160);
            }
        });

        // Vider le cache après création
        static::created(function ($category) {
            Cache::forget('categories.active');
            Cache::forget('categories.featured');
            Cache::forget('categories.menu');

            Log::info('Catégorie créée', [
                'category_id' => $category->id,
                'name' => $category->name,
            ]);
        });

        // Vider le cache après mise à jour
        static::updated(function ($category) {
            Cache::forget('categories.active');
            Cache::forget('categories.featured');
            Cache::forget('categories.menu');
            Cache::forget("category.{$category->id}");

            Log::info('Catégorie mise à jour', [
                'category_id' => $category->id,
                'name' => $category->name,
                'changes' => array_keys($category->getChanges()),
            ]);
        });

        // Supprimer l'image lors de la suppression de la catégorie
        static::deleting(function ($category) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            Cache::forget('categories.active');
            Cache::forget('categories.featured');
            Cache::forget('categories.menu');
            Cache::forget("category.{$category->id}");

            Log::info('Catégorie supprimée', [
                'category_id' => $category->id,
                'name' => $category->name,
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Produits de cette catégorie
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Produits actifs uniquement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeProducts()
    {
        return $this->hasMany(Product::class)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order');
    }

    /**
     * Catégorie parente (pour les catégories hiérarchiques)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Sous-catégories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Sous-catégories actives
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeChildren()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (GETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir l'URL complète de l'image
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        // Image par défaut avec UI Avatars
        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&size=400&background=4F46E5&color=ffffff&bold=true";
    }

    /**
     * Obtenir l'URL complète de la catégorie
     *
     * @return string
     */
    public function getFullUrlAttribute()
    {
        return route('categories.show', $this->slug);
    }

    /**
     * Obtenir le nombre de produits actifs
     *
     * @return int
     */
    public function getProductsCountAttribute()
    {
        // Utiliser le count chargé si disponible, sinon compter
        if ($this->relationLoaded('products')) {
            return $this->products->where('is_active', true)->count();
        }

        return $this->activeProducts()->count();
    }

    /**
     * Obtenir une description courte
     *
     * @return string|null
     */
    public function getShortDescriptionAttribute()
    {
        if ($this->description) {
            return Str::limit(strip_tags($this->description), 150);
        }

        return null;
    }

    /**
     * Obtenir le statut formaté
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    /**
     * Obtenir le badge CSS du statut
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        return $this->is_active
            ? 'bg-green-100 text-green-800 border-green-300'
            : 'bg-red-100 text-red-800 border-red-300';
    }

    /**
     * Obtenir les initiales de la catégorie
     *
     * @return string
     */
    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS (SETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Définir le nom et générer automatiquement le slug
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(trim($value));
        
        // Ne générer le slug que si pas encore défini
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
     * Définir la description avec nettoyage HTML
     *
     * @param string|null $value
     * @return void
     */
    public function setDescriptionAttribute($value)
    {
        // Nettoyage HTML basique (ou utiliser HTMLPurifier)
        $this->attributes['description'] = $value ? strip_tags($value, '<p><br><strong><em><ul><ol><li>') : null;
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope pour les catégories actives
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour les catégories inactives
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInactive(Builder $query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope pour les catégories vedettes
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope pour les catégories principales (sans parent)
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeParent(Builder $query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope pour recherche
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
              ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    /**
     * Scope avec comptage des produits
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithProductsCount(Builder $query)
    {
        return $query->withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }]);
    }

    /**
     * Scope avec produits actifs seulement
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithActiveProducts(Builder $query)
    {
        return $query->with(['products' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }]);
    }

    /**
     * Scope triées par ordre de tri
     *
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrdered(Builder $query, $direction = 'asc')
    {
        return $query->orderBy('sort_order', $direction)->orderBy('name', $direction);
    }

    /**
     * Scope avec minimum de produits
     *
     * @param Builder $query
     * @param int $min
     * @return Builder
     */
    public function scopeHasMinProducts(Builder $query, $min = 1)
    {
        return $query->has('products', '>=', $min);
    }

    /**
     * Scope catégories avec images
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithImages(Builder $query)
    {
        return $query->whereNotNull('image');
    }

    /**
     * Scope catégories sans images
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutImages(Builder $query)
    {
        return $query->whereNull('image');
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES PERSONNALISÉES
    |--------------------------------------------------------------------------
    */

    /**
     * Vérifier si la catégorie a des produits
     *
     * @return bool
     */
    public function hasProducts()
    {
        return $this->products()->exists();
    }

    /**
     * Vérifier si la catégorie a des produits actifs
     *
     * @return bool
     */
    public function hasActiveProducts()
    {
        return $this->activeProducts()->exists();
    }

    /**
     * Obtenir le prix minimum des produits de la catégorie
     *
     * @return float|null
     */
    public function getMinPrice()
    {
        return $this->activeProducts()
            ->selectRaw('COALESCE(MIN(promotional_price), MIN(price)) as min_price')
            ->value('min_price');
    }

    /**
     * Obtenir le prix maximum des produits de la catégorie
     *
     * @return float|null
     */
    public function getMaxPrice()
    {
        return $this->activeProducts()
            ->selectRaw('COALESCE(MAX(promotional_price), MAX(price)) as max_price')
            ->value('max_price');
    }

    /**
     * Obtenir le chemin complet de la catégorie (pour les hiérarchies)
     *
     * @return string
     */
    public function getFullPath()
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Activer la catégorie
     *
     * @return bool
     */
    public function activate()
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Désactiver la catégorie
     *
     * @return bool
     */
    public function deactivate()
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Basculer le statut actif/inactif
     *
     * @return bool
     */
    public function toggleStatus()
    {
        return $this->update(['is_active' => !$this->is_active]);
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
     * Basculer le statut vedette
     *
     * @return bool
     */
    public function toggleFeatured()
    {
        return $this->update(['is_featured' => !$this->is_featured]);
    }

    /**
     * Dupliquer la catégorie
     *
     * @return static
     */
    public function duplicate()
    {
        $newCategory = $this->replicate();
        $newCategory->name = $this->name . ' (Copie)';
        $newCategory->slug = Str::slug($newCategory->name);
        $newCategory->is_active = false;
        $newCategory->save();

        return $newCategory;
    }

    /**
     * Supprimer l'image de la catégorie
     *
     * @return bool
     */
    public function deleteImage()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            Storage::disk('public')->delete($this->image);
        }

        return $this->update(['image' => null]);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES STATIQUES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir toutes les catégories pour un select
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getForSelect()
    {
        return Cache::remember('categories.for_select', 3600, function () {
            return static::active()
                ->ordered()
                ->pluck('name', 'id');
        });
    }

    /**
     * Obtenir les catégories populaires (avec le plus de produits)
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getPopular($limit = 6)
    {
        return Cache::remember("categories.popular.{$limit}", 1800, function () use ($limit) {
            return static::active()
                ->withCount(['products' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->having('products_count', '>', 0)
                ->orderBy('products_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Obtenir les catégories vedettes
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFeatured($limit = 6)
    {
        return Cache::remember("categories.featured.{$limit}", 3600, function () use ($limit) {
            return static::active()
                ->featured()
                ->withCount(['products' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->ordered()
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Recherche intelligente de catégories
     *
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function smartSearch($term)
    {
        return static::active()
            ->search($term)
            ->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->ordered()
            ->get();
    }

    /**
     * Obtenir les statistiques des catégories
     *
     * @return array
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'active' => static::active()->count(),
            'inactive' => static::inactive()->count(),
            'featured' => static::featured()->count(),
            'with_products' => static::has('products')->count(),
            'empty' => static::doesntHave('products')->count(),
            'with_images' => static::withImages()->count(),
        ];
    }
}

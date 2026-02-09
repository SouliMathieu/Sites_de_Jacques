<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'is_featured',
        'show_in_menu',
        'sort_order',
        'parent_id',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'show_in_menu' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'full_url',
    ];

    protected $attributes = [
        'is_active' => true,
        'is_featured' => false,
        'show_in_menu' => true,
        'sort_order' => 0,
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            $originalSlug = $category->slug;
            $count = 1;
            while (static::where('slug', $category->slug)->exists()) {
                $category->slug = $originalSlug . '-' . $count;
                $count++;
            }

            if (empty($category->meta_title)) {
                $category->meta_title = $category->name;
            }

            if (empty($category->meta_description) && $category->description) {
                $category->meta_description = Str::limit(strip_tags($category->description), 160);
            }
        });

        static::created(function ($category) {
            Cache::forget('categories.active');
            Cache::forget('categories.featured');
        });

        static::updated(function ($category) {
            Cache::forget('categories.active');
            Cache::forget('categories.featured');
            Cache::forget("category.{$category->id}");
        });

        static::deleting(function ($category) {
            if ($category->image && !filter_var($category->image, FILTER_VALIDATE_URL)) {
                if (Storage::disk('public')->exists('categories/' . $category->image)) {
                    Storage::disk('public')->delete('categories/' . $category->image);
                }
            }
            Cache::forget('categories.active');
            Cache::forget('categories.featured');
            Cache::forget("category.{$category->id}");
        });
    }

    // RELATIONS
    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('sort_order')->orderBy('name');
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function activeChildren()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    // ACCESSORS
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&size=400&background=4F46E5&color=ffffff&bold=true";
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        $imagePath = 'categories/' . $this->image;
        if (Storage::disk('public')->exists($imagePath)) {
            return Storage::disk('public')->url($imagePath);
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&size=400&background=4F46E5&color=ffffff&bold=true";
    }

    public function getFullUrlAttribute()
    {
        return route('categories.show', $this->slug);
    }

    public function getProductsCountAttribute()
    {
        if ($this->relationLoaded('products')) {
            return $this->products->where('is_active', true)->count();
        }
        return $this->activeProducts()->count();
    }

    // SCOPES
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInMenu(Builder $query)
    {
        return $query->where('show_in_menu', true);
    }

    public function scopeOrdered(Builder $query, $direction = 'asc')
    {
        return $query->orderBy('sort_order', $direction)->orderBy('name', $direction);
    }

    // MÉTHODES
    public function activate()
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        return $this->update(['is_active' => false]);
    }

    public function deleteImage()
    {
        if ($this->image && !filter_var($this->image, FILTER_VALIDATE_URL)) {
            if (Storage::disk('public')->exists('categories/' . $this->image)) {
                Storage::disk('public')->delete('categories/' . $this->image);
            }
        }
        return $this->update(['image' => null]);
    }

    public static function getForSelect()
    {
        return Cache::remember('categories.for_select', 3600, function () {
            return static::active()->ordered()->pluck('name', 'id');
        });
    }
}
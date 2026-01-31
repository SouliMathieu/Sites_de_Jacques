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

    // ✅ CORRIGÉ : Gestion correcte des chemins storage
    public function getFirstImageAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            $firstImagePath = $this->images[0];
            // Vérifier dans storage/app/public
            if (file_exists(storage_path('app/public/' . $firstImagePath))) {
                return asset('storage/' . $firstImagePath);
            }
        }
        return asset('images/placeholder-product.jpg');
    }

    public function getFirstVideoAttribute()
    {
        if ($this->videos && count($this->videos) > 0) {
            $firstVideoPath = $this->videos[0];
            if (file_exists(storage_path('app/public/' . $firstVideoPath))) {
                return asset('storage/' . $firstVideoPath);
            }
        }
        return null;
    }

    public function getHasVideosAttribute()
    {
        return $this->videos && count($this->videos) > 0;
    }

    public function getAllImagesAttribute()
    {
        if ($this->images && count($this->images) > 0) {
            return array_filter($this->images, function($imagePath) {
                return file_exists(storage_path('app/public/' . $imagePath));
            });
        }
        return [];
    }

    public function getAllVideosAttribute()
    {
        if ($this->videos && count($this->videos) > 0) {
            return array_filter($this->videos, function($videoPath) {
                return file_exists(storage_path('app/public/' . $videoPath));
            });
        }
        return [];
    }

    // ✅ NOUVEAU : Méthodes pour obtenir les URLs complètes
    public function getImageUrlsAttribute()
    {
        return collect($this->all_images)->map(function($imagePath) {
            return asset('storage/' . $imagePath);
        })->toArray();
    }

    public function getVideoUrlsAttribute()
    {
        return collect($this->all_videos)->map(function($videoPath) {
            return asset('storage/' . $videoPath);
        })->toArray();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'platform', 'status', 'product_ids', 'budget', 'duration_days',
        'target_audience', 'ad_copy', 'campaign_id_google', 'campaign_id_meta',
        'performance_data', 'start_date', 'end_date', 'created_by'
    ];

    protected $casts = [
        'product_ids' => 'array',
        'target_audience' => 'array',
        'performance_data' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function products()
    {
        return Product::whereIn('id', $this->product_ids ?? [])->get();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive()
    {
        return $this->status === 'active' &&
               now()->between($this->start_date, $this->end_date);
    }

    public function getProductNamesAttribute()
    {
        return $this->products()->pluck('name')->implode(', ');
    }
}

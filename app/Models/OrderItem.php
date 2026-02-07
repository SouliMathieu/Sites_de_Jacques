<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_slug',
        'quantity',
        'unit_price',
        'total_price',
        'discount_amount',
        'tax_amount',
        'notes',
    ];

    /**
     * Les attributs qui doivent être castés
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Les attributs ajoutés dynamiquement
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_unit_price',
        'formatted_total_price',
        'subtotal',
        'grand_total',
    ];

    /**
     * Configuration des valeurs par défaut
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'quantity' => 1,
        'discount_amount' => 0,
        'tax_amount' => 0,
    ];

    /**
     * Boot du modèle
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Calculer automatiquement le total lors de la création
        static::creating(function ($orderItem) {
            $orderItem->calculateTotal();
        });

        // Recalculer le total lors de la mise à jour
        static::updating(function ($orderItem) {
            if ($orderItem->isDirty(['quantity', 'unit_price', 'discount_amount'])) {
                $orderItem->calculateTotal();
            }
        });

        // Mettre à jour le total de la commande après sauvegarde
        static::saved(function ($orderItem) {
            if ($orderItem->order) {
                $orderItem->order->updateTotalAmount();
            }
        });

        // Mettre à jour le total de la commande après suppression
        static::deleted(function ($orderItem) {
            if ($orderItem->order) {
                $orderItem->order->updateTotalAmount();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Commande associée
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Produit associé
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (GETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le prix unitaire formaté
     *
     * @return string
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir le total formaté
     *
     * @return string
     */
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Calculer le sous-total (sans réduction ni taxe)
     *
     * @return float
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Calculer le total final (avec réduction et taxe)
     *
     * @return float
     */
    public function getGrandTotalAttribute()
    {
        return $this->total_price + $this->tax_amount;
    }

    /**
     * Obtenir le nom du produit (avec fallback)
     *
     * @return string
     */
    public function getProductNameAttribute()
    {
        // Utiliser le nom sauvegardé ou celui du produit
        return $this->attributes['product_name'] ?? $this->product?->name ?? 'Produit supprimé';
    }

    /**
     * Obtenir le pourcentage de réduction
     *
     * @return float|null
     */
    public function getDiscountPercentageAttribute()
    {
        if ($this->discount_amount > 0 && $this->subtotal > 0) {
            return round(($this->discount_amount / $this->subtotal) * 100, 2);
        }

        return null;
    }

    /**
     * Vérifier si l'article a une réduction
     *
     * @return bool
     */
    public function getHasDiscountAttribute()
    {
        return $this->discount_amount > 0;
    }

    /**
     * Obtenir l'URL du produit
     *
     * @return string|null
     */
    public function getProductUrlAttribute()
    {
        if ($this->product_slug) {
            return route('products.show', $this->product_slug);
        }

        if ($this->product) {
            return route('products.show', $this->product->slug);
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS (SETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Définir la quantité avec validation
     *
     * @param int $value
     * @return void
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = max(1, (int) $value);
    }

    /**
     * Définir le prix unitaire avec validation
     *
     * @param float $value
     * @return void
     */
    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = max(0, (float) $value);
    }

    /**
     * Définir la réduction avec validation
     *
     * @param float|null $value
     * @return void
     */
    public function setDiscountAmountAttribute($value)
    {
        $this->attributes['discount_amount'] = $value ? max(0, (float) $value) : 0;
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope par commande
     *
     * @param Builder $query
     * @param int $orderId
     * @return Builder
     */
    public function scopeByOrder(Builder $query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    /**
     * Scope par produit
     *
     * @param Builder $query
     * @param int $productId
     * @return Builder
     */
    public function scopeByProduct(Builder $query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope avec réductions
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithDiscounts(Builder $query)
    {
        return $query->where('discount_amount', '>', 0);
    }

    /**
     * Scope quantité supérieure à
     *
     * @param Builder $query
     * @param int $quantity
     * @return Builder
     */
    public function scopeQuantityGreaterThan(Builder $query, $quantity)
    {
        return $query->where('quantity', '>', $quantity);
    }

    /**
     * Scope avec relations complètes
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithFullDetails(Builder $query)
    {
        return $query->with(['order', 'product.category']);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Calculer et mettre à jour le total
     *
     * @return void
     */
    public function calculateTotal()
    {
        $subtotal = $this->quantity * $this->unit_price;
        $this->total_price = $subtotal - ($this->discount_amount ?? 0);
    }

    /**
     * Appliquer une réduction en pourcentage
     *
     * @param float $percentage
     * @return bool
     */
    public function applyPercentageDiscount($percentage)
    {
        if ($percentage > 0 && $percentage <= 100) {
            $discountAmount = ($this->subtotal * $percentage) / 100;
            return $this->update(['discount_amount' => $discountAmount]);
        }

        return false;
    }

    /**
     * Appliquer une réduction en montant fixe
     *
     * @param float $amount
     * @return bool
     */
    public function applyFixedDiscount($amount)
    {
        if ($amount > 0 && $amount <= $this->subtotal) {
            return $this->update(['discount_amount' => $amount]);
        }

        return false;
    }

    /**
     * Retirer la réduction
     *
     * @return bool
     */
    public function removeDiscount()
    {
        return $this->update(['discount_amount' => 0]);
    }

    /**
     * Augmenter la quantité
     *
     * @param int $amount
     * @return bool
     */
    public function increaseQuantity($amount = 1)
    {
        return $this->update(['quantity' => $this->quantity + $amount]);
    }

    /**
     * Diminuer la quantité
     *
     * @param int $amount
     * @return bool
     */
    public function decreaseQuantity($amount = 1)
    {
        $newQuantity = max(1, $this->quantity - $amount);
        return $this->update(['quantity' => $newQuantity]);
    }

    /**
     * Dupliquer l'article de commande
     *
     * @return static
     */
    public function duplicate()
    {
        $newItem = $this->replicate();
        $newItem->save();

        return $newItem;
    }

    /**
     * Sauvegarder les informations du produit pour historique
     *
     * @return void
     */
    public function saveProductSnapshot()
    {
        if ($this->product) {
            $this->update([
                'product_name' => $this->product->name,
                'product_slug' => $this->product->slug,
            ]);
        }
    }

    /**
     * Vérifier si le produit existe encore
     *
     * @return bool
     */
    public function productExists()
    {
        return $this->product !== null;
    }

    /**
     * Vérifier si le stock est suffisant
     *
     * @return bool
     */
    public function hasEnoughStock()
    {
        if (!$this->product) {
            return false;
        }

        return $this->product->stock_quantity >= $this->quantity;
    }

    /**
     * Obtenir le montant économisé
     *
     * @return float
     */
    public function getSavingsAmount()
    {
        return $this->discount_amount ?? 0;
    }

    /**
     * Obtenir les détails formatés de l'article
     *
     * @return array
     */
    public function getFormattedDetails()
    {
        return [
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'unit_price' => $this->formatted_unit_price,
            'subtotal' => number_format($this->subtotal, 0, ',', ' ') . ' FCFA',
            'discount' => $this->has_discount ? number_format($this->discount_amount, 0, ',', ' ') . ' FCFA' : null,
            'total' => $this->formatted_total_price,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES STATIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Créer un article de commande depuis un produit
     *
     * @param Order $order
     * @param Product $product
     * @param int $quantity
     * @return static
     */
    public static function createFromProduct(Order $order, Product $product, $quantity = 1)
    {
        $unitPrice = $product->promotional_price ?? $product->price;

        return static::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
        ]);
    }

    /**
     * Créer plusieurs articles depuis un tableau
     *
     * @param Order $order
     * @param array $items
     * @return \Illuminate\Support\Collection
     */
    public static function createMultiple(Order $order, array $items)
    {
        $orderItems = collect();

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            if ($product) {
                $orderItem = static::createFromProduct(
                    $order,
                    $product,
                    $item['quantity'] ?? 1
                );
                
                $orderItems->push($orderItem);
            }
        }

        return $orderItems;
    }

    /**
     * Obtenir les produits les plus vendus
     *
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public static function getTopSellingProducts($limit = 10)
    {
        return static::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->with('product')
            ->get();
    }

    /**
     * Obtenir le chiffre d'affaires par produit
     *
     * @return \Illuminate\Support\Collection
     */
    public static function getRevenueByProduct()
    {
        return static::selectRaw('product_id, SUM(total_price) as total_revenue, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'desc')
            ->with('product')
            ->get();
    }

    /**
     * Calculer le panier moyen
     *
     * @return float
     */
    public static function getAverageItemValue()
    {
        return static::avg('total_price') ?? 0;
    }

    /**
     * Obtenir les statistiques globales
     *
     * @return array
     */
    public static function getStats()
    {
        return [
            'total_items' => static::count(),
            'total_quantity' => static::sum('quantity'),
            'total_revenue' => static::sum('total_price'),
            'total_discounts' => static::sum('discount_amount'),
            'average_item_value' => static::getAverageItemValue(),
            'average_quantity' => static::avg('quantity') ?? 0,
        ];
    }
}

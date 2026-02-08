<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    /**
     * Attributs remplissables en masse
     */
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_company',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_phone',
        'notes',
        'delivery_address',
        'delivery_city',
        'delivery_phone',
        'delivery_notes',
        'ip_address',
        'user_agent',
        'confirmed_at',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
    ];

    /**
     * Casts
     */
    protected $casts = [
        'total_amount'  => 'decimal:2',
        'confirmed_at'  => 'datetime',
        'paid_at'       => 'datetime',
        'shipped_at'    => 'datetime',
        'delivered_at'  => 'datetime',
        'cancelled_at'  => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'deleted_at'    => 'datetime',
    ];

    /**
     * Attributs ajoutés dynamiquement
     */
    protected $appends = [
        'status_label',
        'payment_status_label',
        'status_badge',
        'payment_status_badge',
        'formatted_total',
        'items_count',
        'is_paid',
        'is_delivered',
        'can_cancel',
    ];

    /**
     * Attributs cachés
     */
    protected $hidden = [
        'ip_address',
        'user_agent',
        'deleted_at',
    ];

    /**
     * Valeurs par défaut
     */
    protected $attributes = [
        'status'         => 'pending',
        'payment_status' => 'pending',
    ];

    /**
     * Statuts de commande
     */
    const STATUS_PENDING    = 'pending';
    const STATUS_CONFIRMED  = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';

    /**
     * Statuts de paiement
     */
    const PAYMENT_PENDING  = 'pending';
    const PAYMENT_PAID     = 'paid';
    const PAYMENT_FAILED   = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * Méthodes de paiement
     */
    const PAYMENT_ORANGE_MONEY    = 'orange_money';
    const PAYMENT_MOOV_MONEY      = 'moov_money';
    const PAYMENT_BANK_TRANSFER   = 'bank_transfer';
    const PAYMENT_CASH_ON_DELIVERY = 'cash_on_delivery';

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /** Client de la commande */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /** Articles de la commande */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    /** Historique des statuts (si table présente) */
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_PENDING    => 'En attente',
            self::STATUS_CONFIRMED  => 'Confirmée',
            self::STATUS_PROCESSING => 'En préparation',
            self::STATUS_SHIPPED    => 'Expédiée',
            self::STATUS_DELIVERED  => 'Livrée',
            self::STATUS_CANCELLED  => 'Annulée',
        ];

        return $labels[$this->status] ?? 'Inconnu';
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            self::PAYMENT_PENDING  => 'En attente',
            self::PAYMENT_PAID     => 'Payé',
            self::PAYMENT_FAILED   => 'Échoué',
            self::PAYMENT_REFUNDED => 'Remboursé',
        ];

        return $labels[$this->payment_status] ?? 'Inconnu';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING    => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            self::STATUS_CONFIRMED  => 'bg-blue-100 text-blue-800 border-blue-300',
            self::STATUS_PROCESSING => 'bg-purple-100 text-purple-800 border-purple-300',
            self::STATUS_SHIPPED    => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            self::STATUS_DELIVERED  => 'bg-green-100 text-green-800 border-green-300',
            self::STATUS_CANCELLED  => 'bg-red-100 text-red-800 border-red-300',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    }

    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            self::PAYMENT_PENDING  => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            self::PAYMENT_PAID     => 'bg-green-100 text-green-800 border-green-300',
            self::PAYMENT_FAILED   => 'bg-red-100 text-red-800 border-red-300',
            self::PAYMENT_REFUNDED => 'bg-orange-100 text-orange-800 border-orange-300',
        ];

        return $badges[$this->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getItemsCountAttribute()
    {
        if ($this->relationLoaded('orderItems')) {
            return $this->orderItems->sum('quantity');
        }

        return $this->orderItems()->sum('quantity');
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function getIsDeliveredAttribute()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    public function getCanCancelAttribute()
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ]);
    }

    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            self::PAYMENT_ORANGE_MONEY     => 'Orange Money',
            self::PAYMENT_MOOV_MONEY       => 'Moov Money',
            self::PAYMENT_BANK_TRANSFER    => 'Virement bancaire',
            self::PAYMENT_CASH_ON_DELIVERY => 'Espèces à la livraison',
        ];

        return $labels[$this->payment_method] ?? 'Inconnu';
    }

    public function getAgeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopePending(Builder $query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed(Builder $query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeDelivered(Builder $query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeCancelled(Builder $query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function scopeUnpaid(Builder $query)
    {
        return $query->where('payment_status', self::PAYMENT_PENDING);
    }

    public function scopeByPaymentMethod(Builder $query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeBetweenDates(Builder $query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeToday(Builder $query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek(Builder $query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    public function scopeThisMonth(Builder $query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('order_number', 'like', "%{$search}%")
              ->orWhere('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%")
              ->orWhere('customer_email', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
              });
        });
    }

    public function scopeWithFullDetails(Builder $query)
    {
        return $query->with(['customer', 'orderItems.product']);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES DE GESTION DES STATUTS
    |--------------------------------------------------------------------------
    */

    public function confirm()
    {
        if ($this->status === self::STATUS_PENDING) {
            return $this->update([
                'status'       => self::STATUS_CONFIRMED,
                'confirmed_at' => now(),
            ]);
        }

        return false;
    }

    public function process()
    {
        if (in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return $this->update([
                'status' => self::STATUS_PROCESSING,
            ]);
        }

        return false;
    }

    public function ship()
    {
        if ($this->status === self::STATUS_PROCESSING) {
            return $this->update([
                'status'     => self::STATUS_SHIPPED,
                'shipped_at' => now(),
            ]);
        }

        return false;
    }

    public function deliver()
    {
        if ($this->status === self::STATUS_SHIPPED) {
            return $this->update([
                'status'      => self::STATUS_DELIVERED,
                'delivered_at'=> now(),
            ]);
        }

        return false;
    }

    public function cancel($reason = null)
    {
        if ($this->can_cancel) {
            return $this->update([
                'status'       => self::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'notes'        => ($this->notes ?? '') . "\n\nAnnulée : " . ($reason ?? 'Aucune raison fournie'),
            ]);
        }

        return false;
    }

    public function markAsPaid($reference = null)
    {
        $data = [
            'payment_status' => self::PAYMENT_PAID,
            'paid_at'        => now(),
        ];

        if ($reference) {
            $data['payment_reference'] = $reference;
        }

        if ($this->status === self::STATUS_PENDING) {
            $data['status']       = self::STATUS_CONFIRMED;
            $data['confirmed_at'] = now();
        }

        return $this->update($data);
    }

    public function markPaymentAsFailed()
    {
        return $this->update([
            'payment_status' => self::PAYMENT_FAILED,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /** Recalcule le total à partir des OrderItems (utilisé par OrderItem::saved) */
    public function updateTotalAmount(): void
    {
        $total = $this->orderItems()->sum('total_price');
        $this->total_amount = $total;
        $this->save();
    }

    public function calculateSubtotal()
    {
        return $this->orderItems->sum('total_price');
    }

    public function getTotalQuantity()
    {
        return $this->orderItems->sum('quantity');
    }

    public function hasProduct($productId)
    {
        return $this->orderItems()->where('product_id', $productId)->exists();
    }

    public function getEstimatedDelivery()
    {
        $days = match ($this->delivery_city) {
            'Ouagadougou'    => 1,
            'Bobo-Dioulasso' => 2,
            default          => 3,
        };

        return now()->addDays($days)->format('d/m/Y');
    }

    public function generateReceipt()
    {
        // Implémentation PDF à ajouter si nécessaire
        // return PDF::loadView('orders.receipt', ['order' => $this])->download();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES STATIQUES
    |--------------------------------------------------------------------------
    */

    public static function getStats()
    {
        return [
            'total'         => static::count(),
            'pending'       => static::pending()->count(),
            'confirmed'     => static::confirmed()->count(),
            'delivered'     => static::delivered()->count(),
            'cancelled'     => static::cancelled()->count(),
            'paid'          => static::paid()->count(),
            'unpaid'        => static::unpaid()->count(),
            'total_revenue' => static::paid()->sum('total_amount'),
            'today_revenue' => static::paid()->today()->sum('total_amount'),
            'month_revenue' => static::paid()->thisMonth()->sum('total_amount'),
        ];
    }

    public static function getRecent($limit = 10)
    {
        return static::with(['customer', 'orderItems'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_ORANGE_MONEY     => 'Orange Money',
            self::PAYMENT_MOOV_MONEY       => 'Moov Money',
            self::PAYMENT_BANK_TRANSFER    => 'Virement bancaire',
            self::PAYMENT_CASH_ON_DELIVERY => 'Espèces à la livraison',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING    => 'En attente',
            self::STATUS_CONFIRMED  => 'Confirmée',
            self::STATUS_PROCESSING => 'En préparation',
            self::STATUS_SHIPPED    => 'Expédiée',
            self::STATUS_DELIVERED  => 'Livrée',
            self::STATUS_CANCELLED  => 'Annulée',
        ];
    }
}

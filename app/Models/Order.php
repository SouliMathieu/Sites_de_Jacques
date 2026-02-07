<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs assignables en masse
     *
     * @var array<int, string>
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
     * Les attributs qui doivent être castés
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
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
     * Les attributs cachés
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'ip_address',
        'user_agent',
        'deleted_at',
    ];

    /**
     * Configuration des valeurs par défaut
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
        'payment_status' => 'pending',
    ];

    /**
     * Statuts de commande disponibles
     */
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Statuts de paiement disponibles
     */
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    /**
     * Méthodes de paiement disponibles
     */
    const PAYMENT_ORANGE_MONEY = 'orange_money';
    const PAYMENT_MOOV_MONEY = 'moov_money';
    const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    const PAYMENT_CASH_ON_DELIVERY = 'cash_on_delivery';

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Client de la commande
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Articles de la commande
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    /**
     * Historique des statuts (si vous avez une table order_status_histories)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (GETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le label du statut
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_CONFIRMED => 'Confirmée',
            self::STATUS_PROCESSING => 'En préparation',
            self::STATUS_SHIPPED => 'Expédiée',
            self::STATUS_DELIVERED => 'Livrée',
            self::STATUS_CANCELLED => 'Annulée',
        ];

        return $labels[$this->status] ?? 'Inconnu';
    }

    /**
     * Obtenir le label du statut de paiement
     *
     * @return string
     */
    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            self::PAYMENT_PENDING => 'En attente',
            self::PAYMENT_PAID => 'Payé',
            self::PAYMENT_FAILED => 'Échoué',
            self::PAYMENT_REFUNDED => 'Remboursé',
        ];

        return $labels[$this->payment_status] ?? 'Inconnu';
    }

    /**
     * Obtenir la classe CSS pour le badge de statut
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            self::STATUS_CONFIRMED => 'bg-blue-100 text-blue-800 border-blue-300',
            self::STATUS_PROCESSING => 'bg-purple-100 text-purple-800 border-purple-300',
            self::STATUS_SHIPPED => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            self::STATUS_DELIVERED => 'bg-green-100 text-green-800 border-green-300',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800 border-red-300',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    }

    /**
     * Obtenir la classe CSS pour le badge de statut de paiement
     *
     * @return string
     */
    public function getPaymentStatusBadgeAttribute()
    {
        $badges = [
            self::PAYMENT_PENDING => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            self::PAYMENT_PAID => 'bg-green-100 text-green-800 border-green-300',
            self::PAYMENT_FAILED => 'bg-red-100 text-red-800 border-red-300',
            self::PAYMENT_REFUNDED => 'bg-orange-100 text-orange-800 border-orange-300',
        ];

        return $badges[$this->payment_status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
    }

    /**
     * Obtenir le montant formaté
     *
     * @return string
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir le nombre d'articles
     *
     * @return int
     */
    public function getItemsCountAttribute()
    {
        if ($this->relationLoaded('orderItems')) {
            return $this->orderItems->sum('quantity');
        }

        return $this->orderItems()->sum('quantity');
    }

    /**
     * Vérifier si la commande est payée
     *
     * @return bool
     */
    public function getIsPaidAttribute()
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Vérifier si la commande est livrée
     *
     * @return bool
     */
    public function getIsDeliveredAttribute()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    /**
     * Vérifier si la commande peut être annulée
     *
     * @return bool
     */
    public function getCanCancelAttribute()
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
        ]);
    }

    /**
     * Obtenir le label de la méthode de paiement
     *
     * @return string
     */
    public function getPaymentMethodLabelAttribute()
    {
        $labels = [
            self::PAYMENT_ORANGE_MONEY => 'Orange Money',
            self::PAYMENT_MOOV_MONEY => 'Moov Money',
            self::PAYMENT_BANK_TRANSFER => 'Virement bancaire',
            self::PAYMENT_CASH_ON_DELIVERY => 'Espèces à la livraison',
        ];

        return $labels[$this->payment_method] ?? 'Inconnu';
    }

    /**
     * Obtenir la durée depuis la création
     *
     * @return string
     */
    public function getAgeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope pour les commandes en attente
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePending(Builder $query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope pour les commandes confirmées
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeConfirmed(Builder $query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    /**
     * Scope pour les commandes livrées
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeDelivered(Builder $query)
    {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    /**
     * Scope pour les commandes annulées
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeCancelled(Builder $query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope pour les commandes payées
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePaid(Builder $query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    /**
     * Scope pour les commandes non payées
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnpaid(Builder $query)
    {
        return $query->where('payment_status', self::PAYMENT_PENDING);
    }

    /**
     * Scope par méthode de paiement
     *
     * @param Builder $query
     * @param string $method
     * @return Builder
     */
    public function scopeByPaymentMethod(Builder $query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope pour une période donnée
     *
     * @param Builder $query
     * @param Carbon $from
     * @param Carbon $to
     * @return Builder
     */
    public function scopeBetweenDates(Builder $query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope pour aujourd'hui
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeToday(Builder $query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope pour cette semaine
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeThisWeek(Builder $query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope pour ce mois
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeThisMonth(Builder $query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
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

    /**
     * Scope avec relations complètes
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithFullDetails(Builder $query)
    {
        return $query->with(['customer', 'orderItems.product']);
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES DE GESTION DES STATUTS
    |--------------------------------------------------------------------------
    */

    /**
     * Confirmer la commande
     *
     * @return bool
     */
    public function confirm()
    {
        if ($this->status === self::STATUS_PENDING) {
            return $this->update([
                'status' => self::STATUS_CONFIRMED,
                'confirmed_at' => now(),
            ]);
        }

        return false;
    }

    /**
     * Marquer comme en cours de traitement
     *
     * @return bool
     */
    public function process()
    {
        if (in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED])) {
            return $this->update([
                'status' => self::STATUS_PROCESSING,
            ]);
        }

        return false;
    }

    /**
     * Marquer comme expédiée
     *
     * @return bool
     */
    public function ship()
    {
        if ($this->status === self::STATUS_PROCESSING) {
            return $this->update([
                'status' => self::STATUS_SHIPPED,
                'shipped_at' => now(),
            ]);
        }

        return false;
    }

    /**
     * Marquer comme livrée
     *
     * @return bool
     */
    public function deliver()
    {
        if ($this->status === self::STATUS_SHIPPED) {
            return $this->update([
                'status' => self::STATUS_DELIVERED,
                'delivered_at' => now(),
            ]);
        }

        return false;
    }

    /**
     * Annuler la commande
     *
     * @param string|null $reason
     * @return bool
     */
    public function cancel($reason = null)
    {
        if ($this->can_cancel) {
            return $this->update([
                'status' => self::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'notes' => $this->notes . "\n\nAnnulée : " . ($reason ?? 'Aucune raison fournie'),
            ]);
        }

        return false;
    }

    /**
     * Marquer le paiement comme effectué
     *
     * @param string|null $reference
     * @return bool
     */
    public function markAsPaid($reference = null)
    {
        $data = [
            'payment_status' => self::PAYMENT_PAID,
            'paid_at' => now(),
        ];

        if ($reference) {
            $data['payment_reference'] = $reference;
        }

        // Confirmer automatiquement si en attente
        if ($this->status === self::STATUS_PENDING) {
            $data['status'] = self::STATUS_CONFIRMED;
            $data['confirmed_at'] = now();
        }

        return $this->update($data);
    }

    /**
     * Marquer le paiement comme échoué
     *
     * @return bool
     */
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

    /**
     * Calculer le sous-total
     *
     * @return float
     */
    public function calculateSubtotal()
    {
        return $this->orderItems->sum('total_price');
    }

    /**
     * Obtenir le nombre total de produits
     *
     * @return int
     */
    public function getTotalQuantity()
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Vérifier si la commande contient un produit spécifique
     *
     * @param int $productId
     * @return bool
     */
    public function hasProduct($productId)
    {
        return $this->orderItems()->where('product_id', $productId)->exists();
    }

    /**
     * Obtenir le délai de livraison estimé
     *
     * @return string
     */
    public function getEstimatedDelivery()
    {
        $days = match ($this->delivery_city) {
            'Ouagadougou' => 1,
            'Bobo-Dioulasso' => 2,
            default => 3,
        };

        return now()->addDays($days)->format('d/m/Y');
    }

    /**
     * Générer un reçu PDF (nécessite un package PDF)
     *
     * @return mixed
     */
    public function generateReceipt()
    {
        // Implémentation avec DomPDF ou autre
        // return PDF::loadView('orders.receipt', ['order' => $this])->download();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES STATIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir les statistiques des commandes
     *
     * @return array
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'pending' => static::pending()->count(),
            'confirmed' => static::confirmed()->count(),
            'delivered' => static::delivered()->count(),
            'cancelled' => static::cancelled()->count(),
            'paid' => static::paid()->count(),
            'unpaid' => static::unpaid()->count(),
            'total_revenue' => static::paid()->sum('total_amount'),
            'today_revenue' => static::paid()->today()->sum('total_amount'),
            'month_revenue' => static::paid()->thisMonth()->sum('total_amount'),
        ];
    }

    /**
     * Obtenir les commandes récentes
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecent($limit = 10)
    {
        return static::with(['customer', 'orderItems'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir toutes les méthodes de paiement disponibles
     *
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_ORANGE_MONEY => 'Orange Money',
            self::PAYMENT_MOOV_MONEY => 'Moov Money',
            self::PAYMENT_BANK_TRANSFER => 'Virement bancaire',
            self::PAYMENT_CASH_ON_DELIVERY => 'Espèces à la livraison',
        ];
    }

    /**
     * Obtenir tous les statuts disponibles
     *
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_CONFIRMED => 'Confirmée',
            self::STATUS_PROCESSING => 'En préparation',
            self::STATUS_SHIPPED => 'Expédiée',
            self::STATUS_DELIVERED => 'Livrée',
            self::STATUS_CANCELLED => 'Annulée',
        ];
    }
}

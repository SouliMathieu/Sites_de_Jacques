<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs assignables en masse
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'country',
        'postal_code',
        'notes',
        'is_active',
        'is_vip',
        'last_order_at',
        'total_spent',
        'orders_count',
    ];

    /**
     * Les attributs qui doivent être castés
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_vip' => 'boolean',
        'last_order_at' => 'datetime',
        'total_spent' => 'decimal:2',
        'orders_count' => 'integer',
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
        'full_address',
        'formatted_total_spent',
        'customer_since',
        'is_new_customer',
        'loyalty_level',
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
        'is_vip' => false,
        'country' => 'Burkina Faso',
        'total_spent' => 0,
        'orders_count' => 0,
    ];

    /**
     * Boot du modèle
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Nettoyer le téléphone lors de la création
        static::creating(function ($customer) {
            $customer->phone = static::cleanPhoneNumber($customer->phone);
        });

        // Mettre à jour le téléphone lors de la modification
        static::updating(function ($customer) {
            if ($customer->isDirty('phone')) {
                $customer->phone = static::cleanPhoneNumber($customer->phone);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Commandes du client
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    /**
     * Commandes confirmées
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function confirmedOrders()
    {
        return $this->hasMany(Order::class)
            ->whereIn('status', [
                Order::STATUS_CONFIRMED,
                Order::STATUS_PROCESSING,
                Order::STATUS_SHIPPED,
                Order::STATUS_DELIVERED,
            ])
            ->latest();
    }

    /**
     * Commandes payées
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paidOrders()
    {
        return $this->hasMany(Order::class)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->latest();
    }

    /**
     * Dernière commande
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestOrder()
    {
        return $this->hasOne(Order::class)->latestOfMany();
    }

    /**
     * Commande la plus importante
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function largestOrder()
    {
        return $this->hasOne(Order::class)->ofMany('total_amount', 'max');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (GETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir l'adresse complète
     *
     * @return string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->postal_code,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Obtenir le montant total dépensé formaté
     *
     * @return string
     */
    public function getFormattedTotalSpentAttribute()
    {
        return number_format($this->total_spent, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Obtenir la date d'inscription formatée
     *
     * @return string
     */
    public function getCustomerSinceAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    /**
     * Vérifier si c'est un nouveau client
     *
     * @return bool
     */
    public function getIsNewCustomerAttribute()
    {
        return $this->created_at->diffInDays(now()) <= 30;
    }

    /**
     * Obtenir le niveau de fidélité
     *
     * @return string
     */
    public function getLoyaltyLevelAttribute()
    {
        if ($this->is_vip) {
            return 'VIP';
        }

        $totalSpent = $this->total_spent ?? 0;

        if ($totalSpent >= 1000000) {
            return 'Platine';
        } elseif ($totalSpent >= 500000) {
            return 'Or';
        } elseif ($totalSpent >= 200000) {
            return 'Argent';
        } elseif ($totalSpent >= 50000) {
            return 'Bronze';
        }

        return 'Standard';
    }

    /**
     * Obtenir le badge de fidélité (classe CSS)
     *
     * @return string
     */
    public function getLoyaltyBadgeAttribute()
    {
        return match ($this->loyalty_level) {
            'VIP' => 'bg-purple-100 text-purple-800 border-purple-300',
            'Platine' => 'bg-gray-100 text-gray-800 border-gray-300',
            'Or' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'Argent' => 'bg-blue-100 text-blue-800 border-blue-300',
            'Bronze' => 'bg-orange-100 text-orange-800 border-orange-300',
            default => 'bg-green-100 text-green-800 border-green-300',
        };
    }

    /**
     * Obtenir le téléphone formaté
     *
     * @return string
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/\D/', '', $this->phone);
        
        if (strlen($phone) === 8) {
            return substr($phone, 0, 2) . ' ' . substr($phone, 2, 2) . ' ' . substr($phone, 4, 2) . ' ' . substr($phone, 6, 2);
        }

        return $this->phone;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS (SETTERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Définir le nom avec capitalisation
     *
     * @param string $value
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower(trim($value)));
    }

    /**
     * Définir l'email en minuscules
     *
     * @param string|null $value
     * @return void
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }

    /**
     * Définir le téléphone nettoyé
     *
     * @param string $value
     * @return void
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = static::cleanPhoneNumber($value);
    }

    /*
    |--------------------------------------------------------------------------
    | QUERY SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope clients actifs
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope clients VIP
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeVip(Builder $query)
    {
        return $query->where('is_vip', true);
    }

    /**
     * Scope clients ayant passé au moins une commande
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithOrders(Builder $query)
    {
        return $query->has('orders');
    }

    /**
     * Scope clients sans commande
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutOrders(Builder $query)
    {
        return $query->doesntHave('orders');
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
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('company', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%");
        });
    }

    /**
     * Scope par ville
     *
     * @param Builder $query
     * @param string $city
     * @return Builder
     */
    public function scopeByCity(Builder $query, $city)
    {
        return $query->where('city', $city);
    }

    /**
     * Scope clients ayant commandé récemment
     *
     * @param Builder $query
     * @param int $days
     * @return Builder
     */
    public function scopeRecentlyOrdered(Builder $query, $days = 30)
    {
        return $query->where('last_order_at', '>=', now()->subDays($days));
    }

    /**
     * Scope clients inactifs
     *
     * @param Builder $query
     * @param int $days
     * @return Builder
     */
    public function scopeInactive(Builder $query, $days = 90)
    {
        return $query->where(function ($q) use ($days) {
            $q->where('last_order_at', '<', now()->subDays($days))
              ->orWhereNull('last_order_at');
        });
    }

    /**
     * Scope clients par niveau de fidélité
     *
     * @param Builder $query
     * @param float $minSpent
     * @return Builder
     */
    public function scopeBySpending(Builder $query, $minSpent)
    {
        return $query->where('total_spent', '>=', $minSpent);
    }

    /**
     * Scope top clients
     *
     * @param Builder $query
     * @param int $limit
     * @return Builder
     */
    public function scopeTopSpenders(Builder $query, $limit = 10)
    {
        return $query->orderBy('total_spent', 'desc')->limit($limit);
    }

    /**
     * Scope nouveaux clients
     *
     * @param Builder $query
     * @param int $days
     * @return Builder
     */
    public function scopeNew(Builder $query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES UTILITAIRES
    |--------------------------------------------------------------------------
    */

    /**
     * Calculer le total dépensé
     *
     * @return float
     */
    public function calculateTotalSpent()
    {
        return $this->paidOrders()->sum('total_amount');
    }

    /**
     * Mettre à jour les statistiques du client
     *
     * @return bool
     */
    public function updateStats()
    {
        return $this->update([
            'total_spent' => $this->calculateTotalSpent(),
            'orders_count' => $this->orders()->count(),
            'last_order_at' => $this->orders()->latest()->value('created_at'),
        ]);
    }

    /**
     * Obtenir le panier moyen
     *
     * @return float
     */
    public function getAverageOrderValue()
    {
        $ordersCount = $this->paidOrders()->count();
        
        if ($ordersCount === 0) {
            return 0;
        }

        return $this->total_spent / $ordersCount;
    }

    /**
     * Obtenir le nombre de commandes
     *
     * @return int
     */
    public function getTotalOrders()
    {
        return $this->orders()->count();
    }

    /**
     * Obtenir le nombre de commandes payées
     *
     * @return int
     */
    public function getPaidOrdersCount()
    {
        return $this->paidOrders()->count();
    }

    /**
     * Vérifier si le client a déjà commandé
     *
     * @return bool
     */
    public function hasOrdered()
    {
        return $this->orders()->exists();
    }

    /**
     * Vérifier si le client est fidèle (multiple commandes)
     *
     * @param int $minOrders
     * @return bool
     */
    public function isLoyal($minOrders = 3)
    {
        return $this->getTotalOrders() >= $minOrders;
    }

    /**
     * Vérifier si le client est inactif
     *
     * @param int $days
     * @return bool
     */
    public function isInactive($days = 90)
    {
        if (!$this->last_order_at) {
            return true;
        }

        return $this->last_order_at->diffInDays(now()) > $days;
    }

    /**
     * Activer le client
     *
     * @return bool
     */
    public function activate()
    {
        return $this->update(['is_active' => true]);
    }

    /**
     * Désactiver le client
     *
     * @return bool
     */
    public function deactivate()
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Marquer comme VIP
     *
     * @return bool
     */
    public function markAsVip()
    {
        return $this->update(['is_vip' => true]);
    }

    /**
     * Retirer le statut VIP
     *
     * @return bool
     */
    public function removeVipStatus()
    {
        return $this->update(['is_vip' => false]);
    }

    /**
     * Obtenir l'historique des commandes formaté
     *
     * @return array
     */
    public function getOrderHistory()
    {
        return $this->orders()
            ->with('orderItems.product')
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'order_number' => $order->order_number,
                    'date' => $order->created_at->format('d/m/Y'),
                    'total' => $order->formatted_total,
                    'status' => $order->status_label,
                    'items_count' => $order->items_count,
                ];
            })
            ->toArray();
    }

    /**
     * Obtenir les produits favoris (les plus commandés)
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getFavoriteProducts($limit = 5)
    {
        return Product::whereHas('orderItems', function ($query) {
            $query->whereHas('order', function ($q) {
                $q->where('customer_id', $this->id);
            });
        })
        ->withCount(['orderItems' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->where('customer_id', $this->id);
            });
        }])
        ->orderBy('order_items_count', 'desc')
        ->limit($limit)
        ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTHODES STATIQUES
    |--------------------------------------------------------------------------
    */

    /**
     * Nettoyer un numéro de téléphone
     *
     * @param string $phone
     * @return string
     */
    public static function cleanPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques sauf le +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Supprimer le préfixe international si présent
        $cleaned = preg_replace('/^\+226/', '', $cleaned);
        
        return $cleaned;
    }

    /**
     * Trouver par téléphone
     *
     * @param string $phone
     * @return static|null
     */
    public static function findByPhone($phone)
    {
        $cleanPhone = static::cleanPhoneNumber($phone);
        return static::where('phone', $cleanPhone)->first();
    }

    /**
     * Trouver ou créer par téléphone
     *
     * @param array $data
     * @return static
     */
    public static function findOrCreateByPhone(array $data)
    {
        $customer = static::findByPhone($data['phone']);

        if (!$customer) {
            $customer = static::create($data);
        }

        return $customer;
    }

    /**
     * Obtenir les statistiques globales
     *
     * @return array
     */
    public static function getStats()
    {
        return [
            'total' => static::count(),
            'active' => static::active()->count(),
            'vip' => static::vip()->count(),
            'with_orders' => static::withOrders()->count(),
            'without_orders' => static::withoutOrders()->count(),
            'new_this_month' => static::new(30)->count(),
            'inactive' => static::inactive(90)->count(),
            'total_revenue' => static::sum('total_spent'),
        ];
    }

    /**
     * Obtenir les top clients
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopCustomers($limit = 10)
    {
        return static::active()
            ->withOrders()
            ->topSpenders($limit)
            ->get();
    }

    /**
     * Obtenir les clients par ville
     *
     * @return array
     */
    public static function getCustomersByCity()
    {
        return static::selectRaw('city, COUNT(*) as count')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->get()
            ->pluck('count', 'city')
            ->toArray();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory;

    /**
     * Colonnes modifiables en masse.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'country',
    ];

    /**
     * Casts de base (facultatif si tes colonnes existent).
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot du modèle : on nettoie le téléphone.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            $customer->phone = static::cleanPhoneNumber($customer->phone);
        });

        static::updating(function ($customer) {
            if ($customer->isDirty('phone')) {
                $customer->phone = static::cleanPhoneNumber($customer->phone);
            }
        });
    }

    /* RELATIONS SIMPLES */

    public function orders()
    {
        return $this->hasMany(Order::class)->latest();
    }

    /* MUTATORS SIMPLES */

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower(trim($value)));
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower(trim($value)) : null;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = static::cleanPhoneNumber($value);
    }

    /* MÉTHODES STATIQUES UTILES */

    /**
     * Nettoyer un numéro de téléphone.
     */
    public static function cleanPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques sauf le +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);

        // Supprimer le préfixe international +226 si présent
        $cleaned = preg_replace('/^\+226/', '', $cleaned);

        return $cleaned;
    }

    /**
     * Retrouver un client par téléphone.
     */
    public static function findByPhone($phone)
    {
        $cleanPhone = static::cleanPhoneNumber($phone);

        return static::where('phone', $cleanPhone)->first();
    }

    /**
     * Retrouver ou créer un client par téléphone.
     */
    public static function findOrCreateByPhone(array $data)
    {
        $customer = static::findByPhone($data['phone']);

        if (!$customer) {
            $customer = static::create($data);
        }

        return $customer;
    }
}

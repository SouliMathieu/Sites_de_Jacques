<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'bio',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'avatar_url',
        'role_label',
        'initials',
        'is_admin',
        'is_super_admin',
        'status_label',
        'status_badge',
    ];

    protected $attributes = [
        'role' => 'user',
        'is_active' => true,
    ];

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_STAFF = 'staff';
    const ROLE_USER = 'user';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            Cache::forget('users.stats');
            Cache::forget('users.by_role');

            Log::info('Utilisateur créé', [
                'user_id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
            ]);
        });

        static::updated(function ($user) {
            Cache::forget('users.stats');
            Cache::forget('users.by_role');
            Cache::forget("user.{$user->id}");

            Log::info('Utilisateur mis à jour', [
                'user_id' => $user->id,
                'changes' => array_keys($user->getChanges()),
            ]);
        });

        static::deleting(function ($user) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            Log::info('Utilisateur supprimé', [
                'user_id' => $user->id,
                'name' => $user->name,
            ]);
        });
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function unreadNotifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable')
            ->whereNull('read_at');
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return "https://ui-avatars.com/api/?name=" . urlencode($this->name) . "&size=400&background=4F46E5&color=ffffff&bold=true";
    }

    public function getRoleLabelAttribute()
    {
        $labels = [
            self::ROLE_SUPER_ADMIN => 'Super Administrateur',
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_STAFF => 'Personnel',
            self::ROLE_USER => 'Utilisateur',
        ];

        return $labels[$this->role] ?? 'Utilisateur';
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }

    public function getIsAdminAttribute()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function getIsSuperAdminAttribute()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function getRoleBadgeAttribute()
    {
        return match ($this->role) {
            self::ROLE_SUPER_ADMIN => 'bg-purple-100 text-purple-800 border-purple-300',
            self::ROLE_ADMIN => 'bg-red-100 text-red-800 border-red-300',
            self::ROLE_MANAGER => 'bg-blue-100 text-blue-800 border-blue-300',
            self::ROLE_STAFF => 'bg-green-100 text-green-800 border-green-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_active ? 'Actif' : 'Inactif';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->is_active 
            ? 'bg-green-100 text-green-800 border-green-300'
            : 'bg-red-100 text-red-800 border-red-300';
    }

    public function getLastLoginFormattedAttribute()
    {
        return $this->last_login_at ? $this->last_login_at->diffForHumans() : 'Jamais';
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower(trim($value)));
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(trim($value));
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query)
    {
        return $query->where('is_active', false);
    }

    public function scopeAdmins(Builder $query)
    {
        return $query->whereIn('role', [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function scopeSuperAdmins(Builder $query)
    {
        return $query->where('role', self::ROLE_SUPER_ADMIN);
    }

    public function scopeByRole(Builder $query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    public function scopeVerified(Builder $query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified(Builder $query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeRecentlyActive(Builder $query, $days = 7)
    {
        return $query->where('last_login_at', '>=', now()->subDays($days));
    }

    public function scopeStaff(Builder $query)
    {
        return $query->whereIn('role', [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_STAFF,
        ]);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN]);
    }

    public function isManager()
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isStaff()
    {
        return in_array($this->role, [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_STAFF,
        ]);
    }

    public function canAccessAdmin()
    {
        return $this->isStaff() && $this->is_active;
    }

    public function activate()
    {
        return $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        return $this->update(['is_active' => false]);
    }

    public function toggleStatus()
    {
        return $this->update(['is_active' => !$this->is_active]);
    }

    public function changeRole($role)
    {
        $validRoles = [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_MANAGER,
            self::ROLE_STAFF,
            self::ROLE_USER,
        ];

        if (in_array($role, $validRoles)) {
            return $this->update(['role' => $role]);
        }

        return false;
    }

    public function updateLastLogin($ip = null)
    {
        return $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }

    public function updateAvatar($file)
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }

        $path = $file->store('avatars', 'public');

        return $this->update(['avatar' => $path]);
    }

    public function deleteAvatar()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            Storage::disk('public')->delete($this->avatar);
        }

        return $this->update(['avatar' => null]);
    }

    public function hasVerifiedEmail()
    {
        return $this->email_verified_at !== null;
    }

    public function getUnreadNotificationsCount()
    {
        return $this->unreadNotifications()->count();
    }

    public function markAllNotificationsAsRead()
    {
        $this->unreadNotifications()->update(['read_at' => now()]);
    }

    public static function getRoles()
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Administrateur',
            self::ROLE_ADMIN => 'Administrateur',
            self::ROLE_MANAGER => 'Manager',
            self::ROLE_STAFF => 'Personnel',
            self::ROLE_USER => 'Utilisateur',
        ];
    }

    public static function getRolesForSelect()
    {
        return static::getRoles();
    }

    public static function createAdmin(array $data)
    {
        $data['role'] = self::ROLE_ADMIN;
        $data['is_active'] = true;
        $data['email_verified_at'] = now();

        return static::create($data);
    }

    public static function createSuperAdmin(array $data)
    {
        $data['role'] = self::ROLE_SUPER_ADMIN;
        $data['is_active'] = true;
        $data['email_verified_at'] = now();

        return static::create($data);
    }

    public static function getStats()
    {
        return Cache::remember('users.stats', 3600, function () {
            return [
                'total' => static::count(),
                'active' => static::active()->count(),
                'inactive' => static::inactive()->count(),
                'admins' => static::admins()->count(),
                'verified' => static::verified()->count(),
                'unverified' => static::unverified()->count(),
                'recently_active' => static::recentlyActive(7)->count(),
                'new_this_month' => static::whereMonth('created_at', now()->month)->count(),
            ];
        });
    }

    public static function getUsersByRole()
    {
        return Cache::remember('users.by_role', 3600, function () {
            return static::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->orderBy('count', 'desc')
                ->get()
                ->pluck('count', 'role')
                ->toArray();
        });
    }
}

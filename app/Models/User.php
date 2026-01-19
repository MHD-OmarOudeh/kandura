<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Address;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'profile_image',
        'is_active',
    ];
    protected $guard_name = 'web';
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationships
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function coupons()
{
    return $this->belongsToMany(Coupon::class, 'coupon_user')
        ->withPivot(['order_id', 'used_at']);
}
    /**
 * User has one wallet
 */
public function wallet()
{
    return $this->hasOne(Wallet::class);
}

/**
 * Get or create user wallet
 */
public function getOrCreateWallet(): Wallet
{
    return $this->wallet ?? Wallet::create([
        'user_id' => $this->id,
        'balance' => 0,
    ]);
}
    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if ($user->role === 'user' && !$user->hasRole('user')) {
                $user->assignRole('user');
            }
        });
    }



}

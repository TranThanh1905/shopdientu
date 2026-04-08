<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'customer_code',
        'total_orders',
        'total_spent',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ========== BOOT - TỰ ĐỘNG TẠO MÃ KHÁCH HÀNG ==========
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if ($user->role === 'user' && !$user->customer_code) {
                // Tạo mã khách hàng: CUST-000001
                $count = self::where('role', 'user')->count() + 1;
                $user->customer_code = 'CUST-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // ========== RELATIONSHIPS ==========
    
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Cập nhật thống kê mua hàng
     */
    public function updatePurchaseStats(): void
    {
        $this->total_orders = $this->orders()->whereIn('status', ['completed', 'shipping'])->count();
        $this->total_spent = $this->orders()->whereIn('status', ['completed', 'shipping'])->sum('final_amount');
        $this->save();
    }
}
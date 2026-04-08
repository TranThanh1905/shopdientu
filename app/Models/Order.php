<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'user_id',
        'customer_code',
        'fullname',
        'email',
        'phone',
        'address',
        'total_amount',
        'discount_amount',
        'final_amount',
        'status',
        'note',
        'return_reason'
    ];
    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->order_code)) {

                $order->order_code = 'ORD-' 
                    . now()->format('YmdHis') 
                    . '-' 
                    . rand(100, 999);
            }
        });
    }

    // ========== RELATIONSHIPS ==========
    public function confirmationLogs(): HasMany
    {
    return $this->hasMany(OrderConfirmationLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    // ========== STATIC METHODS ==========
    
    public static function getStatusLabels(): array
    {
        return [
            'pending' => 'Chờ xử lý',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'returned' => 'Trả hàng',
            'damaged' => 'Hàng hỏng/lỗi'
        ];
    }

    public static function getStatusColors(): array
    {
        return [
            'pending' => 'warning',
            'confirmed' => 'info',
            'shipping' => 'primary',
            'completed' => 'success',
            'cancelled' => 'secondary',
            'returned' => 'danger',
            'damaged' => 'dark'
        ];
    }

    // ========== ACCESSORS ==========
    
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::getStatusLabels()[$this->status] ?? $this->status
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => self::getStatusColors()[$this->status] ?? 'secondary'
        );
    }
}
<?php
// app/Models/InventoryTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'unit_price',
        'note',
        'order_id'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Nhãn loại giao dịch
     */
    public static function getTypeLabels(): array
    {
        return [
            'in' => 'Nhập kho',
            'out' => 'Xuất kho',
            'damaged' => 'Hàng hỏng',
            'returned' => 'Trả hàng'
        ];
    }
}
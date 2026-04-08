<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'purchase_price',
        'selling_price',
        'discount_percent',
        'final_price'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Tổng tiền của chi tiết đơn hàng
     */
    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->final_price * $this->quantity,
        );
    }

    /**
     * Số tiền giảm giá
     */
    protected function discountAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->selling_price - $this->final_price) * $this->quantity,
        );
    }

    /**
     * Lợi nhuận từ chi tiết đơn hàng này
     */
    protected function profit(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->final_price - $this->purchase_price) * $this->quantity,
        );
    }
}
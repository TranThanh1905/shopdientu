<?php
// app/Models/Inventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'product_id',
        'quantity_in_stock',
        'quantity_sold',
        'quantity_damaged',
        'quantity_returned'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Nhập hàng vào kho
     */
    public function addStock(int $quantity): void
    {
        $this->increment('quantity_in_stock', $quantity);
    }

    /**
     * Xuất hàng khỏi kho (bán)
     */
    public function reduceStock(int $quantity): bool
    {
        if ($this->quantity_in_stock >= $quantity) {
            $this->decrement('quantity_in_stock', $quantity);
            $this->increment('quantity_sold', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Đánh dấu hàng hỏng
     */
    public function markAsDamaged(int $quantity): void
    {
        $this->decrement('quantity_in_stock', $quantity);
        $this->increment('quantity_damaged', $quantity);
    }

    /**
     * Trả hàng vào kho
     */
    public function returnStock(int $quantity): void
    {
        $this->increment('quantity_in_stock', $quantity);
        $this->increment('quantity_returned', $quantity);
        $this->decrement('quantity_sold', $quantity);
    }
}
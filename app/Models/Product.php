<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'purchase_price',    // Giá nhập
        'selling_price',     // Giá bán
        'discount_percent',  // % giảm giá
        'image',
        'description',
        'specifications',
        'status'
    ];

    // ========== RELATIONSHIPS ==========
    
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    // ========== ACCESSORS ==========
    
    /**
     * Giá sau giảm
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->discount_percent > 0) {
                    return $this->selling_price * (1 - $this->discount_percent / 100);
                }
                return $this->selling_price;
            }
        );
    }

    /**
     * Số tiền giảm
     */
    protected function discountAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->selling_price - $this->final_price
        );
    }

    /**
     * Số lượng tồn kho
     */
    protected function stock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->inventory?->quantity_in_stock ?? 0
        );
    }

    /**
     * Lợi nhuận mỗi sản phẩm
     */
    protected function profitPerUnit(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->final_price - $this->purchase_price
        );
    }
}
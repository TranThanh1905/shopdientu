<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryImportDetail extends Model
{
    protected $fillable = [
        'inventory_import_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price',
        'note',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(InventoryImport::class, 'inventory_import_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
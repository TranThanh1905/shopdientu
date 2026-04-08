<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderConfirmationLog extends Model
{
    protected $fillable = [
        'order_id',
        'confirmed_by',
        'old_status',
        'new_status',
        'note',
        'ip_address',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * Nhãn hành động dễ đọc
     */
    public function getActionLabelAttribute(): string
    {
        $labels = Order::getStatusLabels();
        $old = $labels[$this->old_status] ?? $this->old_status;
        $new = $labels[$this->new_status] ?? $this->new_status;
        return "Chuyển từ [{$old}] sang [{$new}]";
    }
}
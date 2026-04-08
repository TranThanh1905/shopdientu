<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryImport extends Model
{
    protected $fillable = [
        'import_code',
        'created_by',
        'total_value',
        'note',
        'status',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(InventoryImportDetail::class);
    }

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', today())->count() + 1;
        return 'IMP-' . $date . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public static function getStatusLabels(): array
    {
        return [
            'draft'     => 'Nháp',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
        ];
    }
}
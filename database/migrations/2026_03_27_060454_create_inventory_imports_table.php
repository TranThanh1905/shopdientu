<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_imports', function (Blueprint $table) {
            $table->id();
            // Mã phiếu nhập: IMP-YYYYMMDD-XXXX
            $table->string('import_code')->unique()->comment('Mã phiếu nhập kho');
            // Người thực hiện nhập kho
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            // Tổng giá trị lô hàng nhập
            $table->unsignedBigInteger('total_value')->default(0)->comment('Tổng giá trị nhập kho');
            // Ghi chú phiếu nhập
            $table->text('note')->nullable();
            // Trạng thái phiếu nhập
            $table->enum('status', ['draft', 'confirmed', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->index('import_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_imports');
    }
};
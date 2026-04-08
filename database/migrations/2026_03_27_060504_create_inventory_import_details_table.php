<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_import_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventory_import_id')
                  ->constrained('inventory_imports')
                  ->onDelete('cascade');

            $table->foreignId('product_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->integer('quantity')->comment('Số lượng nhập');
            $table->unsignedBigInteger('unit_price')->comment('Giá nhập mỗi đơn vị');
            $table->unsignedBigInteger('total_price')->comment('Thành tiền');
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_import_details');
    }
};
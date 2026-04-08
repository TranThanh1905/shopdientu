<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Tên sản phẩm
            $table->string('name');

            // Khóa ngoại danh mục
            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            // GIÁ NHẬP - GIÁ BÁN
            $table->unsignedBigInteger('purchase_price')->comment('Giá nhập');
            $table->unsignedBigInteger('selling_price')->comment('Giá bán');
            $table->unsignedBigInteger('discount_percent')->default(0)->comment('% giảm giá');

            // Ảnh sản phẩm
            $table->string('image')->nullable();

            // Mô tả & cấu hình
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();

            // KHÔNG dùng stock trực tiếp (tính từ kho)
            // $table->integer('stock'); // Đã xóa

            // Trạng thái
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

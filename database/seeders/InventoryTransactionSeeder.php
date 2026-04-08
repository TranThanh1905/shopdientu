<?php
// database/seeders/InventoryTransactionSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\InventoryTransaction;
use Carbon\Carbon;

class InventoryTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::with('inventory')->get();
        $transactionCount = 0;

        foreach ($products as $product) {
            // Tạo 1-3 lần nhập kho trong 90 ngày qua
            $importCount = rand(1, 3);

            for ($i = 0; $i < $importCount; $i++) {
                $quantity = $product->inventory->quantity_in_stock / $importCount;
                $createdAt = Carbon::now()->subDays(rand(1, 90));

                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => 1, // Admin
                    'type' => 'in',
                    'quantity' => round($quantity),
                    'unit_price' => $product->purchase_price,
                    'note' => $this->randomImportNote(),
                    'order_id' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $transactionCount++;
            }

            // Một số sản phẩm có hàng hỏng
            if (rand(0, 4) === 0) { // 20% sản phẩm có hàng hỏng
                $damagedQty = rand(1, 3);
                
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => 2, // Warehouse manager
                    'type' => 'damaged',
                    'quantity' => $damagedQty,
                    'unit_price' => $product->purchase_price,
                    'note' => $this->randomDamagedNote(),
                    'order_id' => null,
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                ]);

                $transactionCount++;
            }
        }

        $this->command->info("✓ Created {$transactionCount} inventory transactions (stock in & damaged)");
    }

    private function randomImportNote(): string
    {
        $notes = [
            'Nhập hàng từ nhà cung cấp chính hãng',
            'Lô hàng tháng ' . Carbon::now()->format('m/Y'),
            'Nhập bổ sung do hết hàng',
            'Nhập hàng đợt khuyến mãi',
            'Đặt hàng từ đại lý ủy quyền',
        ];

        return $notes[array_rand($notes)];
    }

    private function randomDamagedNote(): string
    {
        $notes = [
            'Phát hiện lỗi khi kiểm tra chất lượng',
            'Vỡ hộp trong quá trình vận chuyển',
            'Sản phẩm bị trầy xước nặng',
            'Không qua được kiểm tra QC',
            'Hết hạn bảo quản',
        ];

        return $notes[array_rand($notes)];
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\InventoryTransaction;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Bộ đếm tĩnh để đảm bảo order_code luôn unique
     * ngay cả khi nhiều đơn được tạo trong cùng 1 giây.
     */
    private static int $orderCounter = 1;

    public function run(): void
    {
        $users    = User::where('role', 'user')->get();
        $products = Product::with('inventory')->where('status', 'active')->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠ No users or products found. Skipping orders.');
            return;
        }

        // Lấy số đơn hiện có để counter không bị trùng khi chạy lại
        self::$orderCounter = Order::count() + 1;

        $orderCount = 0;
        $statuses   = [
            'completed', 'completed', 'completed',
            'shipping', 'confirmed', 'pending',
            'returned', 'damaged',
        ];

        for ($i = 0; $i < 30; $i++) {

            $user      = $users->random();
            $status    = $statuses[array_rand($statuses)];
            $createdAt = Carbon::now()->subDays(rand(1, 60));

            // Chọn 1-4 sản phẩm ngẫu nhiên
            $count         = min(rand(1, 4), $products->count());
            $orderProducts = $products->random($count);

            $totalAmount  = 0;
            $totalDiscount = 0;
            $orderDetails = [];

            foreach ($orderProducts as $product) {
                $quantity       = rand(1, 3);
                $sellingPrice   = $product->selling_price;
                $discountPct    = $product->discount_percent;
                $finalPrice     = (int) round($sellingPrice * (1 - $discountPct / 100));
                $discountAmount = ($sellingPrice - $finalPrice) * $quantity;

                $totalAmount   += $sellingPrice * $quantity;
                $totalDiscount += $discountAmount;

                $orderDetails[] = [
                    'product'        => $product,
                    'quantity'       => $quantity,
                    'purchase_price' => $product->purchase_price,
                    'selling_price'  => $sellingPrice,
                    'discount_percent' => $discountPct,
                    'final_price'    => $finalPrice,
                ];
            }

            $finalAmount = $totalAmount - $totalDiscount;

            // ── Tạo order_code unique ──────────────────────────────────
            // Dùng counter tăng dần thay vì chỉ dùng timestamp
            $orderCode = $this->generateOrderCode($createdAt);
            // ──────────────────────────────────────────────────────────

            $order = Order::create([
                'order_code'      => $orderCode,
                'user_id'         => $user->id,
                'customer_code'   => $user->customer_code,
                'fullname'        => $user->name,
                'email'           => $user->email,
                'phone'           => '09' . rand(10000000, 99999999),
                'address'         => $this->randomAddress(),
                'total_amount'    => $totalAmount,
                'discount_amount' => $totalDiscount,
                'final_amount'    => $finalAmount,
                'status'          => $status,
                'note'            => $this->randomNote($status),
                'return_reason'   => in_array($status, ['returned', 'damaged'])
                                        ? $this->randomReturnReason($status)
                                        : null,
                'created_at'      => $createdAt,
                'updated_at'      => $createdAt->copy()->addHours(rand(1, 48)),
            ]);

            // ── Chi tiết đơn hàng + cập nhật kho ──────────────────────
            foreach ($orderDetails as $detail) {
                OrderDetail::create([
                    'order_id'        => $order->id,
                    'product_id'      => $detail['product']->id,
                    'quantity'        => $detail['quantity'],
                    'purchase_price'  => $detail['purchase_price'],
                    'selling_price'   => $detail['selling_price'],
                    'discount_percent'=> $detail['discount_percent'],
                    'final_price'     => $detail['final_price'],
                ]);

                $this->syncInventory($order, $detail, $status, $createdAt);
            }

            // Cập nhật thống kê user nếu hoàn thành
            if ($status === 'completed') {
                $user->increment('total_orders');
                $user->increment('total_spent', $finalAmount);
            }

            $orderCount++;
        }

        $this->command->info("✓ Created {$orderCount} orders with details and inventory transactions");
    }

    // ================================================================
    // HELPERS
    // ================================================================

    /**
     * Tạo order_code theo format: ORD-YYYYMMDD-XXXXXX
     * XXXXXX = counter 6 chữ số, đảm bảo unique tuyệt đối.
     */
    private function generateOrderCode(Carbon $date): string
    {
        // Tăng counter mỗi lần gọi
        $seq = str_pad(self::$orderCounter++, 6, '0', STR_PAD_LEFT);
        return 'ORD-' . $date->format('Ymd') . '-' . $seq;
    }

    /**
     * Xử lý đồng bộ kho theo trạng thái đơn.
     * Bọc trong try/catch để seeder không chết nếu kho âm.
     */
    private function syncInventory(Order $order, array $detail, string $status, Carbon $createdAt): void
    {
        $inventory = $detail['product']->inventory;

        // Chỉ xử lý các trạng thái ảnh hưởng đến kho
        if (!in_array($status, ['confirmed', 'shipping', 'completed', 'returned', 'damaged'])) {
            return;
        }

        try {
            match ($status) {

                'completed', 'confirmed', 'shipping' => (function () use ($inventory, $detail, $order, $createdAt) {
                    // Tránh kho âm khi seed
                    if (($inventory->quantity_in_stock ?? 0) >= $detail['quantity']) {
                        $inventory->decrement('quantity_in_stock', $detail['quantity']);
                    }
                    $inventory->increment('quantity_sold', $detail['quantity']);

                    InventoryTransaction::create([
                        'product_id' => $detail['product']->id,
                        'user_id'    => 1,
                        'type'       => 'out',
                        'quantity'   => $detail['quantity'],
                        'unit_price' => $detail['final_price'],
                        'note'       => "Xuất kho cho đơn hàng {$order->order_code}",
                        'order_id'   => $order->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                })(),

                'returned' => (function () use ($inventory, $detail, $order, $createdAt) {
                    $inventory->increment('quantity_returned', $detail['quantity']);

                    InventoryTransaction::create([
                        'product_id' => $detail['product']->id,
                        'user_id'    => 1,
                        'type'       => 'returned',
                        'quantity'   => $detail['quantity'],
                        'unit_price' => $detail['purchase_price'],
                        'note'       => "Trả hàng: {$order->return_reason}",
                        'order_id'   => $order->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                })(),

                'damaged' => (function () use ($inventory, $detail, $order, $createdAt) {
                    $inventory->increment('quantity_damaged', $detail['quantity']);

                    InventoryTransaction::create([
                        'product_id' => $detail['product']->id,
                        'user_id'    => 1,
                        'type'       => 'damaged',
                        'quantity'   => $detail['quantity'],
                        'unit_price' => $detail['purchase_price'],
                        'note'       => "Hàng hỏng: {$order->return_reason}",
                        'order_id'   => $order->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                })(),

                default => null,
            };

        } catch (\Exception $e) {
            $this->command->warn("  ⚠ Inventory sync skipped for product #{$detail['product']->id}: {$e->getMessage()}");
        }
    }

    private function randomAddress(): string
    {
        $addresses = [
            'Số 123, Đường Láng, Quận Đống Đa, Hà Nội',
            '456 Lê Lợi, Quận 1, TP. Hồ Chí Minh',
            'Tầng 5, Tòa nhà ABC, Cầu Giấy, Hà Nội',
            '789 Trần Hưng Đạo, Quận 5, TP. Hồ Chí Minh',
            'Số 12, Nguyễn Trãi, Thanh Xuân, Hà Nội',
            '234 Võ Văn Tần, Quận 3, TP. Hồ Chí Minh',
            'Tòa CT1, KĐT Văn Khê, Hà Đông, Hà Nội',
            '567 Nguyễn Thị Minh Khai, Quận 3, TP. Hồ Chí Minh',
        ];
        return $addresses[array_rand($addresses)];
    }

    private function randomNote(string $status): ?string
    {
        if (rand(0, 2) > 0) return null;

        $notes = [
            'pending'   => ['Khách hàng yêu cầu gọi trước khi giao', 'Giao giờ hành chính', 'Kiểm tra kỹ hàng trước khi giao'],
            'confirmed' => ['Đã xác nhận với khách hàng', 'Đang chuẩn bị hàng'],
            'shipping'  => ['Đang trên đường giao hàng', 'Shipper đã nhận hàng'],
            'completed' => ['Giao hàng thành công', 'Khách hàng hài lòng'],
            'returned'  => ['Khách hàng trả hàng', 'Đã hoàn tiền'],
            'damaged'   => ['Hàng bị hỏng trong quá trình vận chuyển', 'Sản phẩm lỗi kỹ thuật'],
        ];

        $pool = $notes[$status] ?? [''];
        return $pool[array_rand($pool)];
    }

    private function randomReturnReason(string $status): string
    {
        $reasons = [
            'returned' => [
                'Khách hàng đổi ý, không muốn mua nữa',
                'Sản phẩm không đúng như mô tả',
                'Khách hàng tìm được sản phẩm rẻ hơn',
                'Sản phẩm không vừa ý',
            ],
            'damaged' => [
                'Vỡ màn hình trong quá trình vận chuyển',
                'Sản phẩm lỗi kỹ thuật, không bật được máy',
                'Pin bị phồng',
                'Trầy xước nghiêm trọng',
                'Không đúng màu/phiên bản đã đặt',
            ],
        ];

        $pool = $reasons[$status] ?? ['Không rõ lý do'];
        return $pool[array_rand($pool)];
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ĐIỆN THOẠI
            [
                'name' => 'iPhone 15 Pro Max 256GB',
                'category_id' => 1,
                'purchase_price' => 28000000,
                'selling_price' => 32990000,
                'discount_percent' => 5,
                'image' => 'images/iphone15.jpg',
                'description' => 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ, camera 48MP',
                'specifications' => 'Chip A17 Pro, RAM 8GB, 256GB, Màn hình 6.7" Super Retina XDR, Camera 48MP',
                'status' => 'active',
                'initial_stock' => 50
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra 512GB',
                'category_id' => 1,
                'purchase_price' => 26000000,
                'selling_price' => 30990000,
                'discount_percent' => 8,
                'image' => 'images/samsung-s24.webp',
                'description' => 'Galaxy S24 Ultra với S Pen tích hợp, màn hình Dynamic AMOLED 2X',
                'specifications' => 'Snapdragon 8 Gen 3, RAM 12GB, 512GB, Màn hình 6.8" QHD+, Camera 200MP',
                'status' => 'active',
                'initial_stock' => 40
            ],
            [
                'name' => 'iPhone 14 Pro 128GB',
                'category_id' => 1,
                'purchase_price' => 22000000,
                'selling_price' => 26990000,
                'discount_percent' => 10,
                'image' => 'images/iphone14pro.jpg',
                'description' => 'iPhone 14 Pro với Dynamic Island, camera 48MP Pro',
                'specifications' => 'Chip A16 Bionic, RAM 6GB, 128GB, Màn hình 6.1" Super Retina XDR',
                'status' => 'active',
                'initial_stock' => 35
            ],
            [
                'name' => 'Xiaomi 14 Pro 256GB',
                'category_id' => 1,
                'purchase_price' => 15000000,
                'selling_price' => 18990000,
                'discount_percent' => 5,
                'image' => 'images/xiaomi14pro.jpg',
                'description' => 'Xiaomi 14 Pro với camera Leica, sạc nhanh 120W',
                'specifications' => 'Snapdragon 8 Gen 3, RAM 12GB, 256GB, Màn hình 6.73" AMOLED',
                'status' => 'active',
                'initial_stock' => 30
            ],
            [
                'name' => 'OPPO Find X6 Pro',
                'category_id' => 1,
                'purchase_price' => 18000000,
                'selling_price' => 22990000,
                'discount_percent' => 7,
                'image' => 'images/oppo-findx6.jpg',
                'description' => 'OPPO Find X6 Pro với camera Hasselblad',
                'specifications' => 'Snapdragon 8 Gen 2, RAM 16GB, 256GB, Màn hình 6.82" AMOLED',
                'status' => 'active',
                'initial_stock' => 25
            ],

            // LAPTOP
            [
                'name' => 'MacBook Pro 14" M3 Pro 18GB/512GB',
                'category_id' => 2,
                'purchase_price' => 48000000,
                'selling_price' => 54990000,
                'discount_percent' => 3,
                'image' => 'images/macbook-pro.jpg',
                'description' => 'MacBook Pro 14 inch với chip M3 Pro mạnh mẽ',
                'specifications' => 'Chip M3 Pro 11-core CPU, 14-core GPU, RAM 18GB, SSD 512GB, Màn hình Liquid Retina XDR 14.2"',
                'status' => 'active',
                'initial_stock' => 30
            ],
            [
                'name' => 'Dell XPS 15 9530 i9/32GB/1TB RTX 4070',
                'category_id' => 2,
                'purchase_price' => 52000000,
                'selling_price' => 59990000,
                'discount_percent' => 5,
                'image' => 'images/dell-xps15.webp',
                'description' => 'Dell XPS 15 cao cấp cho đồ họa và gaming',
                'specifications' => 'Intel Core i9-13900H, RAM 32GB, SSD 1TB, RTX 4070 8GB, Màn hình 15.6" 4K OLED',
                'status' => 'active',
                'initial_stock' => 20
            ],
            [
                'name' => 'ASUS ROG Zephyrus G14 Ryzen 9/RTX 4060',
                'category_id' => 2,
                'purchase_price' => 38000000,
                'selling_price' => 44990000,
                'discount_percent' => 8,
                'image' => 'images/asus-rog-g14.jpg',
                'description' => 'Laptop gaming mỏng nhẹ với hiệu năng cao',
                'specifications' => 'AMD Ryzen 9 7940HS, RAM 16GB, SSD 1TB, RTX 4060 8GB, Màn hình 14" QHD+ 165Hz',
                'status' => 'active',
                'initial_stock' => 25
            ],
            [
                'name' => 'Lenovo ThinkPad X1 Carbon Gen 11',
                'category_id' => 2,
                'purchase_price' => 42000000,
                'selling_price' => 48990000,
                'discount_percent' => 4,
                'image' => 'images/thinkpad-x1.jpg',
                'description' => 'Laptop doanh nhân cao cấp, bền bỉ',
                'specifications' => 'Intel Core i7-1355U, RAM 32GB, SSD 1TB, Màn hình 14" 2.8K OLED',
                'status' => 'active',
                'initial_stock' => 15
            ],
            [
                'name' => 'HP Spectre x360 14" i7/16GB/1TB',
                'category_id' => 2,
                'purchase_price' => 36000000,
                'selling_price' => 42990000,
                'discount_percent' => 6,
                'image' => 'images/hp-spectre.jpg',
                'description' => 'Laptop 2-in-1 cao cấp với thiết kế sang trọng',
                'specifications' => 'Intel Core i7-1355U, RAM 16GB, SSD 1TB, Màn hình 13.5" 3K2K cảm ứng',
                'status' => 'active',
                'initial_stock' => 18
            ],

            // TAI NGHE
            [
                'name' => 'AirPods Pro 2 (USB-C)',
                'category_id' => 3,
                'purchase_price' => 5200000,
                'selling_price' => 6490000,
                'discount_percent' => 8,
                'image' => 'images/airpods-pro.webp',
                'description' => 'Tai nghe Apple AirPods Pro thế hệ 2 với chip H2',
                'specifications' => 'Chống ồn chủ động, Chip H2, Âm thanh thích ứng, Spatial Audio, Pin 30h',
                'status' => 'active',
                'initial_stock' => 100
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'category_id' => 3,
                'purchase_price' => 7000000,
                'selling_price' => 8990000,
                'discount_percent' => 10,
                'image' => 'images/sony-wh1000xm5.jpg',
                'description' => 'Tai nghe chống ồn hàng đầu từ Sony',
                'specifications' => 'Chống ồn AI, 8 Microphones, LDAC, Pin 40h, Multipoint',
                'status' => 'active',
                'initial_stock' => 60
            ],
            [
                'name' => 'Bose QuietComfort Ultra',
                'category_id' => 3,
                'purchase_price' => 8500000,
                'selling_price' => 10490000,
                'discount_percent' => 5,
                'image' => 'images/bose-qc-ultra.jpg',
                'description' => 'Tai nghe Bose cao cấp với Immersive Audio',
                'specifications' => 'Chống ồn thế hệ mới, Spatial Audio, Pin 24h',
                'status' => 'active',
                'initial_stock' => 40
            ],
            [
                'name' => 'Jabra Elite 10',
                'category_id' => 3,
                'purchase_price' => 4500000,
                'selling_price' => 5990000,
                'discount_percent' => 12,
                'image' => 'images/jabra-elite10.jpg',
                'description' => 'Tai nghe true wireless cao cấp từ Jabra',
                'specifications' => 'Chống ồn ANC, Dolby Audio, Pin 36h (với case), IP57',
                'status' => 'active',
                'initial_stock' => 70
            ],

            // MÀN HÌNH
            [
                'name' => 'LG UltraGear 27" 4K 144Hz',
                'category_id' => 4,
                'purchase_price' => 12000000,
                'selling_price' => 14990000,
                'discount_percent' => 7,
                'image' => 'images/lg-ultragear-27.jpg',
                'description' => 'Màn hình gaming 4K với tần số quét 144Hz',
                'specifications' => '27", 4K UHD (3840x2160), IPS, 144Hz, 1ms, HDR 600, G-Sync',
                'status' => 'active',
                'initial_stock' => 35
            ],
            [
                'name' => 'Samsung Odyssey G9 49" Curved',
                'category_id' => 4,
                'purchase_price' => 28000000,
                'selling_price' => 32990000,
                'discount_percent' => 5,
                'image' => 'images/samsung-odyssey-g9.jpg',
                'description' => 'Màn hình cong siêu rộng 49 inch cho gaming',
                'specifications' => '49", 5120x1440, VA Curved 1000R, 240Hz, 1ms, HDR 1000',
                'status' => 'active',
                'initial_stock' => 15
            ],
            [
                'name' => 'Dell UltraSharp U2723DE 27" QHD',
                'category_id' => 4,
                'purchase_price' => 9000000,
                'selling_price' => 11490000,
                'discount_percent' => 8,
                'image' => 'images/dell-ultrasharp.jpg',
                'description' => 'Màn hình chuyên nghiệp cho thiết kế',
                'specifications' => '27", QHD (2560x1440), IPS Black, 100% sRGB, USB-C 90W, KVM',
                'status' => 'active',
                'initial_stock' => 30
            ],

            // BÀN PHÍM
            [
                'name' => 'Keychron K8 Pro QMK/VIA Wireless',
                'category_id' => 5,
                'purchase_price' => 2800000,
                'selling_price' => 3490000,
                'discount_percent' => 10,
                'image' => 'images/keychron-k8-pro.jpg',
                'description' => 'Bàn phím cơ không dây tùy biến cao',
                'specifications' => 'TKL Layout, Hot-swappable, QMK/VIA, RGB, Pin 4000mAh, Mac/Win',
                'status' => 'active',
                'initial_stock' => 50
            ],
            [
                'name' => 'Logitech MX Keys S',
                'category_id' => 5,
                'purchase_price' => 2200000,
                'selling_price' => 2890000,
                'discount_percent' => 5,
                'image' => 'images/logitech-mx-keys.jpg',
                'description' => 'Bàn phím văn phòng cao cấp',
                'specifications' => 'Full-size, Backlit, Multi-device (3), Logi Bolt, Pin 10 ngày',
                'status' => 'active',
                'initial_stock' => 40
            ],

            // CHUỘT
            [
                'name' => 'Logitech G Pro X Superlight 2',
                'category_id' => 6,
                'purchase_price' => 3200000,
                'selling_price' => 3990000,
                'discount_percent' => 8,
                'image' => 'images/logitech-gpro-x2.jpg',
                'description' => 'Chuột gaming siêu nhẹ chuyên nghiệp',
                'specifications' => '60g, Hero 2 32K DPI, Lightspeed, Pin 95h, Switches quang học',
                'status' => 'active',
                'initial_stock' => 60
            ],
            [
                'name' => 'Razer DeathAdder V3 Pro',
                'category_id' => 6,
                'purchase_price' => 3000000,
                'selling_price' => 3790000,
                'discount_percent' => 10,
                'image' => 'images/razer-deathadder-v3.jpg',
                'description' => 'Chuột gaming không dây cao cấp từ Razer',
                'specifications' => '63g, Focus Pro 30K DPI, HyperSpeed Wireless, Pin 90h, RGB',
                'status' => 'active',
                'initial_stock' => 55
            ],

            // PHỤ KIỆN
            [
                'name' => 'Anker PowerCore 20000mAh 65W',
                'category_id' => 7,
                'purchase_price' => 1200000,
                'selling_price' => 1590000,
                'discount_percent' => 12,
                'image' => 'images/anker-powercore.jpg',
                'description' => 'Pin sạc dự phòng công suất cao 65W',
                'specifications' => '20000mAh, USB-C PD 65W, 2 cổng USB-A, Màn hình LED',
                'status' => 'active',
                'initial_stock' => 100
            ],
            [
                'name' => 'Ugreen Nexode 100W GaN Charger',
                'category_id' => 7,
                'purchase_price' => 900000,
                'selling_price' => 1290000,
                'discount_percent' => 15,
                'image' => 'images/ugreen-nexode.jpg',
                'description' => 'Sạc nhanh GaN 100W 4 cổng',
                'specifications' => '100W, 3 USB-C + 1 USB-A, GaN Technology, Compact',
                'status' => 'active',
                'initial_stock' => 80
            ],
        ];

        foreach ($products as $productData) {
            $initialStock = $productData['initial_stock'];
            unset($productData['initial_stock']);

            $product = Product::create($productData);

            // Tạo inventory
            Inventory::create([
                'product_id' => $product->id,
                'quantity_in_stock' => $initialStock,
                'quantity_sold' => 0,
                'quantity_damaged' => 0,
                'quantity_returned' => 0
            ]);
        }

        $this->command->info('✓ Created ' . count($products) . ' products with inventory');
    }
}
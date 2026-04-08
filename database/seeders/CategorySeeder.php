<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Điện thoại',
                'description' => 'Điện thoại di động các thương hiệu nổi tiếng',
                'status' => 'active'
            ],
            [
                'name' => 'Laptop',
                'description' => 'Máy tính xách tay cho công việc và giải trí',
                'status' => 'active'
            ],
            [
                'name' => 'Tai nghe',
                'description' => 'Tai nghe có dây và không dây chất lượng cao',
                'status' => 'active'
            ],
            [
                'name' => 'Màn hình',
                'description' => 'Màn hình máy tính độ phân giải cao',
                'status' => 'active'
            ],
            [
                'name' => 'Bàn phím',
                'description' => 'Bàn phím cơ và bàn phím văn phòng',
                'status' => 'active'
            ],
            [
                'name' => 'Chuột',
                'description' => 'Chuột gaming và chuột văn phòng',
                'status' => 'active'
            ],
            [
                'name' => 'Phụ kiện',
                'description' => 'Phụ kiện điện tử đa dạng',
                'status' => 'active'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('✓ Created ' . count($categories) . ' categories');
    }
}
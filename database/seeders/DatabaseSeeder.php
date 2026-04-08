<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        $this->call([
            CategorySeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            InventoryTransactionSeeder::class, // Nhập kho trước
            OrderSeeder::class, // Đơn hàng sau (sẽ tự động trừ kho)
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        
        $this->command->table(
            ['Resource', 'Status'],
            [
                ['Categories', '✓ Seeded'],
                ['Users (Admin + Customers)', '✓ Seeded'],
                ['Products with Inventory', '✓ Seeded'],
                ['Inventory Transactions', '✓ Seeded'],
                ['Orders with Details', '✓ Seeded'],
            ]
        );

        $this->command->newLine();
        $this->command->info('📝 Login credentials:');
        $this->command->line('   Admin: admin@electroshop.vn / admin123');
        $this->command->line('   Warehouse: warehouse@electroshop.vn / admin123');
        $this->command->line('   Customer: nguyenvana@gmail.com / user123');
        $this->command->newLine();
    }
}
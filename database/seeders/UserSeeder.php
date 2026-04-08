<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin users
        $admins = [
            [
                'name' => 'Admin',
                'email' => 'admin@electroshop.vn',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Quản lý kho',
                'email' => 'warehouse@electroshop.vn',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']], // check theo email
                $admin
            );
        }

        // Regular users
        $users = [
            [
                'name' => 'Nguyễn Văn A',
                'email' => 'user@gmail.com',
                'role' => 'user',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders' => 0,
                'total_spent' => 0,
            ],
            [
                'name' => 'Trần Thị B',
                'email' => 'tranthib@gmail.com',
                'role' => 'user',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders' => 0,
                'total_spent' => 0,
            ],
            [
                'name' => 'Lê Văn C',
                'email' => 'levanc@gmail.com',
                'role' => 'user',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders' => 0,
                'total_spent' => 0,
            ],
            [
                'name' => 'Phạm Thị D',
                'email' => 'phamthid@gmail.com',
                'role' => 'user',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders' => 0,
                'total_spent' => 0,
            ],
            [
                'name' => 'Hoàng Văn E',
                'email' => 'hoangvane@gmail.com',
                'role' => 'user',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders' => 0,
                'total_spent' => 0,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']], // check theo email
                $user
            );
        }

        $this->command->info('✓ Users seeded successfully');
        $this->command->info('  - Admin: admin@electroshop.vn / admin123');
        $this->command->info('  - Warehouse: warehouse@electroshop.vn / admin123');
        $this->command->info('  - Users: *@gmail.com / 123456');
    }
}

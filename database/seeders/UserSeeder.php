<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // ADMIN
        // ==========================================
        $admins = [
            [
                'name'              => 'Admin',
                'email'             => 'admin@electroshop.vn',
                'role'              => 'admin',
                'password'          => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Quản lý kho',
                'email'             => 'warehouse@electroshop.vn',
                'role'              => 'admin',
                'password'          => Hash::make('admin123'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($admins as $data) {
            User::create($data);
        }

        // ==========================================
        // MOB — Trung gian (chỉ xem + xác nhận)
        // ==========================================
        $mobs = [
            [
                'name'              => 'Nguyễn Thị Mob',
                'email'             => 'mob@electroshop.vn',
                'role'              => 'mob',
                'password'          => Hash::make('mob123'),
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Trần Văn Trung Gian',
                'email'             => 'mob2@electroshop.vn',
                'role'              => 'mob',
                'password'          => Hash::make('mob123'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($mobs as $data) {
            User::create($data);
        }

        // ==========================================
        // USER — Khách hàng thường
        // Mã khách hàng tự sinh trong boot() của Model User
        // ==========================================
        $users = [
            [
                'name'              => 'Nguyễn Văn A',
                'email'             => 'nguyenvana@gmail.com',
                'role'              => 'user',
                'password'          => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders'      => 0,
                'total_spent'       => 0,
            ],
            [
                'name'              => 'Trần Thị B',
                'email'             => 'tranthib@gmail.com',
                'role'              => 'user',
                'password'          => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders'      => 0,
                'total_spent'       => 0,
            ],
            [
                'name'              => 'Lê Văn C',
                'email'             => 'levanc@gmail.com',
                'role'              => 'user',
                'password'          => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders'      => 0,
                'total_spent'       => 0,
            ],
            [
                'name'              => 'Phạm Thị D',
                'email'             => 'phamthid@gmail.com',
                'role'              => 'user',
                'password'          => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders'      => 0,
                'total_spent'       => 0,
            ],
            [
                'name'              => 'Hoàng Văn E',
                'email'             => 'hoangvane@gmail.com',
                'role'              => 'user',
                'password'          => Hash::make('123456'),
                'email_verified_at' => now(),
                'total_orders'      => 0,
                'total_spent'       => 0,
            ],
        ];

        foreach ($users as $data) {
            User::create($data);
        }

        // ==========================================
        // THỐNG KÊ
        // ==========================================
        $totalAdmin = count($admins);
        $totalMob   = count($mobs);
        $totalUser  = count($users);

        $this->command->newLine();
        $this->command->info('✅ UserSeeder hoàn thành!');
        $this->command->table(
            ['Role', 'Số lượng', 'Email mẫu', 'Mật khẩu'],
            [
                ['Admin', $totalAdmin, 'admin@electroshop.vn',     'admin123'],
                ['Mob',   $totalMob,   'mob@electroshop.vn',       'mob123'],
                ['User',  $totalUser,  'nguyenvana@gmail.com', '123456'],
            ]
        );
        $this->command->newLine();
    }
}
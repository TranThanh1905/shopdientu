<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    public function up(): void
    {
        // MySQL không ALTER ENUM trực tiếp, dùng DB::statement
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'mob') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin') NOT NULL DEFAULT 'user'");
    }
};
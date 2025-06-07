<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 创建管理员账号
        User::create([
            'username' => 'admin',
            'real_name' => '管理员',
            'email' => 'admin@study.com',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 创建测试教师账号
        User::create([
            'username' => 'teacher',
            'real_name' => '测试教师',
            'email' => 'teacher@study.com',
            'password' => Hash::make('teacher123'),
            'role' => 'teacher',
            'email_verified_at' => now(),
        ]);

        // 创建测试学生账号
        User::create([
            'username' => 'student',
            'real_name' => '测试学生',
            'email' => 'student@study.com',
            'password' => Hash::make('student123'),
            'role' => 'student',
            'email_verified_at' => now(),
        ]);

        // 创建默认用户
        $this->call([
            UserSeeder::class,
            ResourceSeeder::class,
        ]);
    }
}

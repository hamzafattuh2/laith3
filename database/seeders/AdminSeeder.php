<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // إنشاء مستخدم مسؤول
        $adminUser = User::create([
            // 'name'=>'hamza',
            'user_name' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin12@example.com',
            'password' => Hash::make('admin123'), // كلمة مرور قوية في الواقع
            'type' => 'admin',
            'phone_number' => '1234567890',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
        ]);

        // إنشاء سجل في جدول admins
        Admin::create([
            'user_id' => $adminUser->id,
            'role' => 'super_admin', // يمكنك تغيير هذا حسب نظام الأدوار لديك
        ]);

        // يمكنك إضافة المزيد من المسؤولين هنا إذا لزم الأمر
        $this->command->info('Admin user created successfully!');
    }
}

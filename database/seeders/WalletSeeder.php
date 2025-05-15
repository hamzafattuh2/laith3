<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;

class WalletSeeder extends Seeder
{
    public function run()
    {
        // إنشاء محفظة لكل مستخدم موجود
        $users = User::all();

        foreach ($users as $user) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 1000.00 // رصيد ابتدائي
            ]);
        }
    }
}

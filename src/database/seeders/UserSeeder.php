<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
        'id' => 1,    
        'name' => '出品者A',
        'email' => 'sellerA@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'zipcode' => '100-0001',
        'address' => '東京都千代田区1-1-1',
        'building' => '出品者Aビル',
        ]);

        User::create([
        'id' => 2,    
        'name' => '出品者B',
        'email' => 'sellerB@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'zipcode' => '150-0001',
        'address' => '東京都渋谷区1-1-1',
        'building' => '出品者Bマンション',
        ]);

        User::create([
        'id' => 3,    
        'name' => '購入者',
        'email' => 'buyer@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        'zipcode' => '160-0001',
        'address' => '東京都新宿区1-1-1',
        'building' => '購入者アパート',
        ]);
    }
}

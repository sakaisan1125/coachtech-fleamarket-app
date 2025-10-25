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
        ]);

        User::create([
        'id' => 2,    
        'name' => '出品者B',
        'email' => 'sellerB@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        ]);

        User::create([
        'id' => 3,    
        'name' => '購入者',
        'email' => 'buyer@example.com',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
        ]);
    }
}

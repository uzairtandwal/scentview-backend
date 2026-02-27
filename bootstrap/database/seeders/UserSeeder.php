<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Create Karein
        User::create([
            'name' => 'Waseem Admin',
            'email' => 'waseem@gmail.com',
            'password' => Hash::make('password123'), // Aap apni marzi ka password rakhen
            'role' => 'admin',
            'phone_number' => '03079417399',
            'address' => 'ScentView Head Office, Pakistan',
        ]);

        // 2. Ek Sample User Create Karein (Testing ke liye)
        User::create([
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'phone_number' => '03001234567',
            'address' => 'Sample Street, Lahore',
        ]);
    }
}
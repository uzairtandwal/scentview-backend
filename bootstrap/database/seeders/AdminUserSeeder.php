<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'waseem@gmail.com'],
            [
                'name' => 'Waseem Admin', // <-- یہ فیلڈ شامل کی گئی ہے
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        // Optional test user
        User::updateOrCreate(
            ['email' => 'uzair@gmail.com'],
            [
                'name' => 'Uzair Test User', // <-- یہ فیلڈ شامل کی گئی ہے
                'password' => Hash::make('12345678'),
                'role' => 'user',
            ]
        );
    }
}
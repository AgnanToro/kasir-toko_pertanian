<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Kasir',
            'email' => 'admin@kasir.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@kasir.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@kasir.com',
            'password' => Hash::make('password123'),
        ]);
    }
}

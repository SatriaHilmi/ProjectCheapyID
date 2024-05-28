<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin'),
            'roles' => 'admin',
            'alamat' => '123 Main Street',
            'phone' => '1234567890',
        ]);

        // Add more users if needed
        User::create([
            'name' => 'user',
            'email' => 'user@gmail.com',
            'password' => Hash::make('1'),
            'roles' => 'user',
            'alamat' => '456 Elm Street',
            'phone' => '0987654321',
        ]);
    }
}

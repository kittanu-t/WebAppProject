<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'active' => true,
        ]);

        User::firstOrCreate(['email' => 'owner@example.com'], [
            'name' => 'Field Owner',
            'password' => Hash::make('Owner@123'),
            'role' => 'staff',
            'active' => true,
        ]);

        User::firstOrCreate(['email' => 'user@example.com'], [
            'name' => 'Student User',
            'password' => Hash::make('User@123'),
            'role' => 'user',
            'active' => true,
        ]);
    }
}


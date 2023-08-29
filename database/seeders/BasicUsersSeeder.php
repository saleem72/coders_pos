<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BasicUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'super_admin@admin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role_id' => 1,
            'is_active' => true,
            'is_verified' => true
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role_id' => 2,
            'is_active' => true,
            'is_verified' => true
        ]);

        User::create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role_id' => 2,
            'is_active' => true,
            'is_verified' => true
        ]);
    }
}

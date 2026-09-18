<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthDevelopmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Bendahara Account
        User::updateOrCreate(
            ['email' => 'bendahara@kasma.edu'],
            [
                'name' => 'Nadya Putri',
                'nim' => '220400',
                'password' => Hash::make('password'),
                'role' => 'bendahara',
                'phone_number' => '082198765432',
                'is_active' => true,
            ]
        );

        // 2. Sample Mahasiswa 1
        User::updateOrCreate(
            ['email' => 'hafizh@kasma.edu'],
            [
                'name' => 'Hafizh Al-Fatih',
                'nim' => '220401',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'phone_number' => '081234567890',
                'is_active' => true,
            ]
        );

        // 3. Sample Mahasiswa 2
        User::updateOrCreate(
            ['email' => 'farhan@kasma.edu'],
            [
                'name' => 'Farhan Pratama',
                'nim' => '220412',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'phone_number' => '081234567891',
                'is_active' => true,
            ]
        );

        // 4. Sample Mahasiswa 3
        User::updateOrCreate(
            ['email' => 'siti@kasma.edu'],
            [
                'name' => 'Siti Nurhaliza',
                'nim' => '220425',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'phone_number' => '081234567892',
                'is_active' => true,
            ]
        );
    }
}

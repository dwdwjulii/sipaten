<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data user yang ada sebelumnya agar tidak duplikat
        // User::truncate();

        // Buat User Admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@email.com',
            'password' => Hash::make('12345678'), // Ganti 'password' dengan password aman Anda
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        // Buat User Petugas
        User::create([
            'name' => 'Petugas',
            'email' => 'petugas@email.com',
            'password' => Hash::make('12345678'), // Ganti 'password' dengan password aman Anda
            'role' => 'petugas',
            'status' => 'aktif',
        ]);
    }
}
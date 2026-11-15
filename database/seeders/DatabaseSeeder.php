<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuario Administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@italfiat.com'],
            [
                'name' => 'Soria, Sergio',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ],
        );

        // Asegura el rol aunque el usuario ya existiera de antes.
        $admin->update(['role' => User::ROLE_ADMIN]);

        // 2. Usuario Vendedor
        User::firstOrCreate(
            ['email' => 'vendedor@italfiat.com'],
            [
                'name' => 'Vendedor Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_VENDEDOR,
            ],
        );

        // 3. Usuario Depósito
        User::firstOrCreate(
            ['email' => 'deposito@italfiat.com'],
            [
                'name' => 'Depósito Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DEPOSITO,
            ],
        );
    }
}
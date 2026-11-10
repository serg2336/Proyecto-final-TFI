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
<<<<<<< HEAD
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

        User::firstOrCreate(
            ['email' => 'vendedor@italfiat.com'],
            [
                'name' => 'Vendedor Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_VENDEDOR,
            ],
        );

        User::firstOrCreate(
            ['email' => 'deposito@italfiat.com'],
            [
                'name' => 'Depósito Demo',
                'password' => Hash::make('password'),
                'role' => User::ROLE_DEPOSITO,
            ],
        );
=======
        User::factory()->create([
            'name' => 'Soria, Sergio',
            'email' => 'admin@italfiat.com',
            'password' => Hash::make('password'),
        ]);
>>>>>>> b9250cf4d4c3d27f7445291093ec889bf4f836d1
    }
}

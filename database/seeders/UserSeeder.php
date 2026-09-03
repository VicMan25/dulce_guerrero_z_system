<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // En producción solo siembra si se pide explícitamente (ALLOW_USER_SEED=true).
        // Así un re-seed accidental no recrea el admin por defecto.
        if (app()->environment('production') && ! env('ALLOW_USER_SEED', false)) {
            $this->command?->warn('UserSeeder omitido en producción (define ALLOW_USER_SEED=true para forzarlo).');
            return;
        }

        User::firstOrCreate(
            ['email' => 'admin@dulceg.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('admin1234'),
                'role'     => 'administrador',
            ]
        );

        User::firstOrCreate(
            ['email' => 'empleado@dulceg.com'],
            [
                'name'     => 'Empleado',
                'password' => Hash::make('empleado1234'),
                'role'     => 'empleado',
            ]
        );
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
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
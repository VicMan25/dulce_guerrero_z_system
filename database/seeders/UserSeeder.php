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
            'name'     => 'Administrador',
            'email'    => 'admin@dulceg.com',
            'password' => Hash::make('admin1234'),
            'role'     => 'administrador',
        ]);

        User::create([
            'name'     => 'Empleado',
            'email'    => 'empleado@dulceg.com',
            'password' => Hash::make('empleado1234'),
            'role'     => 'empleado',
        ]);
    }
}
<?php

namespace Database\Seeders;

use App\Models\Insumo;
use Illuminate\Database\Seeder;

class InsumoSeeder extends Seeder
{
    public function run(): void
    {
        $insumos = [
            ['nombre' => 'Harina',          'unidad_de_medida' => 'gramos',     'stock' => 5000, 'stock_minimo' => 500],
            ['nombre' => 'Azúcar',          'unidad_de_medida' => 'gramos',     'stock' => 3000, 'stock_minimo' => 300],
            ['nombre' => 'Mantequilla',     'unidad_de_medida' => 'gramos',     'stock' => 2000, 'stock_minimo' => 200],
            ['nombre' => 'Huevos',          'unidad_de_medida' => 'unidades',   'stock' => 60,   'stock_minimo' => 10],
            ['nombre' => 'Leche',           'unidad_de_medida' => 'mililitros', 'stock' => 5000, 'stock_minimo' => 500],
            ['nombre' => 'Sal',             'unidad_de_medida' => 'gramos',     'stock' => 1000, 'stock_minimo' => 100],
            ['nombre' => 'Levadura',        'unidad_de_medida' => 'gramos',     'stock' => 500,  'stock_minimo' => 50],
            ['nombre' => 'Chocolate',       'unidad_de_medida' => 'gramos',     'stock' => 2000, 'stock_minimo' => 200],
            ['nombre' => 'Vainilla',        'unidad_de_medida' => 'mililitros', 'stock' => 500,  'stock_minimo' => 50],
            ['nombre' => 'Crema de leche',  'unidad_de_medida' => 'mililitros', 'stock' => 2000, 'stock_minimo' => 200],
            ['nombre' => 'Azúcar pulverizada', 'unidad_de_medida' => 'gramos', 'stock' => 1000, 'stock_minimo' => 100],
            ['nombre' => 'Cacao en polvo',  'unidad_de_medida' => 'gramos',     'stock' => 1000, 'stock_minimo' => 100],
        ];

        foreach ($insumos as $data) {
            Insumo::firstOrCreate(
                ['nombre' => $data['nombre']],
                array_merge($data, ['activo' => true])
            );
        }
    }
}

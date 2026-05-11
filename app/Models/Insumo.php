<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    protected $table = 'insumos';
    protected $primaryKey = 'id_insumo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'unidad_de_medida',
        'stock',
        'stock_minimo',
        'activo',
    ];
}
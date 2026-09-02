<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DetalleReceta;

class Insumo extends Model
{
    use SoftDeletes;

    protected $table = 'insumos';
    protected $primaryKey = 'id_insumo';
    public $timestamps = false;

    // Solo se maneja deleted_at; created_at/updated_at no existen en la tabla.
    protected $casts = ['deleted_at' => 'datetime'];

    protected $fillable = [
        'nombre',
        'unidad_de_medida',
        'stock',
        'stock_minimo',
        'activo',
    ];

    public function detallesReceta()
    {
        return $this->hasMany(DetalleReceta::class, 'id_insumo', 'id_insumo');
    }
}
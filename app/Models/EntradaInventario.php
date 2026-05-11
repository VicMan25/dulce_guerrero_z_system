<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntradaInventario extends Model
{
    protected $table = 'entradas_inventario';
    protected $primaryKey = 'id_entrada';

    protected $fillable = [
        'id_insumo',
        'cantidad',
        'fecha',
        'observacion',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo', 'id_insumo');
    }
}

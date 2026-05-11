<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleReceta extends Model
{
    protected $table = 'detalle_receta';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_receta',
        'id_insumo',
        'cantidad',
    ];

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'id_receta', 'id_receta');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'id_insumo', 'id_insumo');
    }
}
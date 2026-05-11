<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    protected $table = 'recetas';
    protected $primaryKey = 'id_receta';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'id_producto',
        'activo',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleReceta::class, 'id_receta', 'id_receta');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'precio',
        'activo',
    ];

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'id_producto', 'id_producto');
    }
}
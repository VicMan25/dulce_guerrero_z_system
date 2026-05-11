<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConteoInventario extends Model
{
    protected $table = 'conteos_inventario';
    protected $primaryKey = 'id_conteo';
    protected $fillable = ['fecha', 'observacion'];

    public function detalles()
    {
        return $this->hasMany(DetalleConteo::class, 'id_conteo', 'id_conteo');
    }
}

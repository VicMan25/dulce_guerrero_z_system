<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleConteo extends Model
{
    protected $table = 'detalles_conteo';
    protected $primaryKey = 'id_detalle';
    protected $fillable = ['id_conteo', 'id_insumo', 'stock_anterior', 'stock_nuevo', 'diferencia'];

    public function insumo()
    {
        // withTrashed: el detalle del conteo sigue mostrando el insumo aunque se haya eliminado.
        return $this->belongsTo(Insumo::class, 'id_insumo', 'id_insumo')->withTrashed();
    }
}

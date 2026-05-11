<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoManual extends Model
{
    protected $table = 'ingresos_manuales';
    protected $primaryKey = 'id_ingreso';
    protected $fillable = ['monto', 'descripcion', 'fecha', 'categoria'];
}

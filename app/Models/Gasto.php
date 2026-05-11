<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';
    protected $primaryKey = 'id_gasto';

    protected $fillable = [
        'descripcion',
        'monto',
        'fecha',
        'categoria',
    ];
}

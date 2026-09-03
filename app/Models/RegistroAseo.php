<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroAseo extends Model
{
    protected $table = 'registros_aseo';

    protected $fillable = [
        'actividad', 'user_id', 'nombre_usuario', 'fecha', 'motivo', 'nota', 'ciclo', 'registrado_por',
    ];

    protected $casts = ['fecha' => 'date'];

    public const MOTIVO_TURNO = 'turno';
    public const MOTIVO_LLEGADA_TARDE = 'llegada_tarde';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registrador()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}

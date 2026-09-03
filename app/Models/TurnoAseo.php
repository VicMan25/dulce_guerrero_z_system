<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TurnoAseo extends Model
{
    protected $table = 'turnos_aseo';

    protected $fillable = ['actividad', 'user_id', 'orden', 'activo'];

    protected $casts = ['activo' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

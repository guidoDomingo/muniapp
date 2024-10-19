<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $table = 'solicitudes'; // Actualiza aquí el nombre de la tabla

    protected $fillable = ['user_id', 'tramite_id', 'detalles', 'estado', 'latitud', 'longitud', 'formulario'];

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


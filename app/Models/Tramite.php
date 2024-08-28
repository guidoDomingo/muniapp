<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }
}


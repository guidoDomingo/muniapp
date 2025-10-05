<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TramiteStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'order',
        'is_final',
    ];

    protected $casts = [
        'is_final' => 'boolean',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'status_id');
    }
}
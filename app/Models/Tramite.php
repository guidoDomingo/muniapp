<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tramite extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'department_id',
        'form_fields',
        'required_documents',
        'estimated_days',
        'cost',
        'is_active',
        'include_map',
        'workflow_steps',
    ];

    protected $casts = [
        'form_fields' => 'array',
        'required_documents' => 'array',
        'workflow_steps' => 'array',
        'is_active' => 'boolean',
        'include_map' => 'boolean',
        'cost' => 'decimal:2',
    ];

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getActiveSolicitudesCountAttribute()
    {
        return $this->solicitudes()->whereNotIn('estado', ['completado', 'rechazado'])->count();
    }
}


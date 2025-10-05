<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'user_id',
        'tramite_id',
        'status_id',
        'detalles',
        'estado',
        'latitud',
        'longitud',
        'formulario',
        'attachments',
        'priority',
        'estimated_completion_date',
        'actual_completion_date',
        'assigned_to',
        'tracking_code',
    ];

    protected $casts = [
        'formulario' => 'array',
        'attachments' => 'array',
        'estimated_completion_date' => 'date',
        'actual_completion_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($solicitud) {
            $solicitud->tracking_code = 'SOL-' . date('Y') . '-' . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        });
    }

    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(TramiteStatus::class, 'status_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function history()
    {
        return $this->hasMany(SolicitudHistory::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'room', 'tracking_code');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('estado', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pendiente' => 'warning',
            'en_revision' => 'info',
            'aprobado' => 'success',
            'rechazado' => 'danger',
            'en_proceso' => 'primary',
            'completado' => 'success',
        ];

        return $colors[$this->estado] ?? 'secondary';
    }

    public function getDaysFromCreationAttribute()
    {
        return $this->created_at->diffInDays(now());
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitud_id',
        'user_id',
        'action',
        'old_value',
        'new_value',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Crear una entrada de historial
     */
    public static function createEntry($solicitudId, $userId, $action, $oldValue = null, $newValue = null, $description = null, $metadata = null)
    {
        return static::create([
            'solicitud_id' => $solicitudId,
            'user_id' => $userId,
            'action' => $action,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}
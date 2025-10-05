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
        'previous_status_id',
        'new_status_id',
        'comments',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function previousStatus()
    {
        return $this->belongsTo(TramiteStatus::class, 'previous_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(TramiteStatus::class, 'new_status_id');
    }
}
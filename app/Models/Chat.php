<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chat extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'message',
        'room',
        'chat_type',
        'parent_id',
        'attachments',
        'is_read',
        'solicitud_id',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_read' => 'boolean',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Chat::class, 'parent_id');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_participants')
                   ->withTimestamps()
                   ->withPivot('joined_at', 'left_at', 'is_admin');
    }

    public function scopeByRoom($query, $room)
    {
        return $query->where('room', $room);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('chat_type', $type);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('H:i');
    }

    public function getIsOwnMessageAttribute()
    {
        return $this->user_id === auth()->id();
    }
}

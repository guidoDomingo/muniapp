<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'type',
        'created_by',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_room_participants')
                   ->withTimestamps()
                   ->withPivot('role', 'joined_at', 'last_read_at', 'is_active', 'permissions');
    }

    public function messages()
    {
        return $this->hasMany(Chat::class, 'room', 'name');
    }

    public function latestMessage()
    {
        return $this->hasOne(Chat::class, 'room', 'name')->latest();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function isParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function addParticipant($userId, $role = 'member')
    {
        return $this->participants()->attach($userId, [
            'role' => $role,
            'joined_at' => now(),
        ]);
    }

    public function removeParticipant($userId)
    {
        return $this->participants()->detach($userId);
    }
}
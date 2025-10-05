<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'phone',
        'address',
        'dni',
        'department_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isCommission()
    {
        return $this->hasRole('commission');
    }

    public function isUser()
    {
        return $this->hasRole('user');
    }

    public function isFunctionary()
    {
        return $this->hasRole('functionary');
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/avatars/' . $this->avatar) : asset('images/default-avatar.png');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}

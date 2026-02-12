<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPassword;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */protected $fillable = [
        'name', 'email', 'password', 'display_name', 'full_name', 'twitter', 'instagram', 'homepage', 'avatar_path', 'is_admin', 'is_moderator', 'banned_at', 'banned_until'];

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path) {
            return \Illuminate\Support\Facades\Storage::url($this->avatar_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random&color=fff';
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_moderator' => 'boolean',
        'banned_at' => 'datetime',
        'banned_until' => 'datetime',
    ];

    public function isModerator()
    {
        return $this->is_admin || $this->is_moderator;
    }

    public function isBanned()
    {
        return $this->banned_until && $this->banned_until->isFuture();
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }

    public function getRoleColorAttribute()
    {
        if ($this->is_admin)
            return '#ff6b6b'; // Red
        if ($this->is_moderator)
            return '#2ecc71'; // Green
        return 'inherit';
    }

    public function scopes()
    {
        return $this->belongsToMany(\App\Models\Scope::class , 'scope_user');
    }


    public function groups()
    {
        return $this->belongsToMany(\App\Models\Group::class , 'group_user')->withTimestamps();
    }

    public function isOnline()
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }

    public function sentMessages()
    {
        return $this->hasMany(\App\Models\Message::class , 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(\App\Models\Message::class , 'receiver_id');
    }


}

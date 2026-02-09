<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */protected $fillable = [
        'name', 'email', 'password', 'display_name', 'full_name', 'twitter', 'instagram', 'homepage', 'is_admin', 'is_moderator', 'banned_at', 'banned_until'];


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
        if ($this->banned_at) {
            // Permanent ban if banned_until is null, or temporary ban if banned_until is in future
            if ($this->banned_until === null) {
                return true;
            }
            return $this->banned_until->isFuture();
        }
        return false;
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

    public function sendEmailVerificationNotification()
    {
        try {
            $this->notify(new \App\Notifications\CustomVerifyEmail);
            \Illuminate\Support\Facades\Log::info('Verification email sent to ' . $this->email);
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send verification email: ' . $e->getMessage());
        }
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

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
        'name', 'email', 'password', 'display_name', 'full_name', 'twitter', 'instagram', 'homepage', 'is_admin'];


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
    ];
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
}

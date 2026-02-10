<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(User::class , 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class , 'group_user')
            ->withPivot('status')
            ->withTimestamps()
            ->wherePivot('status', 'approved');
    }

    public function pendingMembers()
    {
        return $this->belongsToMany(User::class , 'group_user')
            ->withPivot('status')
            ->withTimestamps()
            ->wherePivot('status', 'pending');
    }

    public function allMembers()
    {
        return $this->belongsToMany(User::class , 'group_user')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function posts()
    {
        return $this->hasMany(Post::class)->orderBy('created_at', 'desc');
    }
}

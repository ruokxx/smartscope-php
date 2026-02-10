<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'is_pinned',
        'is_locked',
        'view_count'
    ];

    public function category()
    {
        return $this->belongsTo(ForumCategory::class , 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(ForumPost::class , 'thread_id');
    }

    public function latestPost()
    {
        return $this->hasOne(ForumPost::class , 'thread_id')->latest();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'order', 'parent_id'];

    public function threads()
    {
        return $this->hasMany(ForumThread::class , 'category_id');
    }

    public function children()
    {
        return $this->hasMany(ForumCategory::class , 'parent_id')->orderBy('order');
    }

    public function parent()
    {
        return $this->belongsTo(ForumCategory::class , 'parent_id');
    }
}

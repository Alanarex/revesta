<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'blog_id',
        'user_id',
        'parent_id',
        'content',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'parent_id')->orderBy('created_at', 'asc');
    }

    public function likes()
    {
        return $this->morphMany(BlogLike::class, 'likeable');
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }
}

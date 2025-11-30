<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'short_description',
        'content',
        'status',
        'rejection_reason',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Status constants
    public const DRAFT = 'draft';
    public const PENDING = 'pending';
    public const PUBLISHED = 'published';
    public const REJECTED = 'rejected';

    /**
     * Helpers for status checks
     */
    public function isDraft(): bool
    {
        return $this->status === self::DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }

    public function isPublished(): bool
    {
        return $this->status === self::PUBLISHED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::REJECTED;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class)->whereNull('parent_id')->orderBy('created_at', 'desc');
    }

    public function allComments()
    {
        return $this->hasMany(BlogComment::class);
    }

    public function likes()
    {
        return $this->morphMany(BlogLike::class, 'likeable');
    }

    public function bookmarks()
    {
        return $this->hasMany(BlogBookmark::class);
    }

    public function isLikedBy($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isBookmarkedBy($userId)
    {
        return $this->bookmarks()->where('user_id', $userId)->exists();
    }

    public function getTimeAgoAttribute()
    {
        return $this->published_at ? $this->published_at->diffForHumans() : $this->created_at->diffForHumans();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeSearchByTitle($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('short_description', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }
        return $query;
    }
}

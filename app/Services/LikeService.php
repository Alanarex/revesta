<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LikeService
{
    /**
     * Toggle like on a likeable model (Blog or BlogComment).
     */
    public function toggleLike(User $user, Model $likeable): array
    {
        // If the controller preloaded `liked_by_auth` and `likes_count`, use them
        // to avoid extra EXISTS()/COUNT() queries. Otherwise fallback to checks.
        $preloadedLiked = $likeable->getAttribute('liked_by_auth') ?? null;
        $preloadedCount = $likeable->getAttribute('likes_count') ?? null;

        if ($preloadedLiked !== null) {
            $alreadyLiked = ((int)$preloadedLiked) > 0;
        } else {
            $alreadyLiked = $likeable->likes()->where('user_id', $user->id)->exists();
        }

        if ($alreadyLiked) {
            // remove
            $likeable->likes()->where('user_id', $user->id)->delete();
            $liked = false;
            $count = $preloadedCount !== null ? max(0, (int)$preloadedCount - 1) : $likeable->likes()->count();
        } else {
            // create
            $likeable->likes()->create([
                'user_id' => $user->id
            ]);
            $liked = true;
            $count = $preloadedCount !== null ? (int)$preloadedCount + 1 : $likeable->likes()->count();
        }

        return [
            'liked' => $liked,
            'count' => $count
        ];
    }

    /**
     * Get likes count for a model.
     */
    public function getLikesCount(Model $likeable): int
    {
        return $likeable->likes()->count();
    }

    /**
     * Check if user has liked the model.
     */
    public function hasLiked(User $user, Model $likeable): bool
    {
        return $likeable->likes()
            ->where('user_id', $user->id)
            ->exists();
    }
}

<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BlogLikeRepository
{
    /**
     * Toggle like on a likeable model (Blog or BlogComment).
     */
    public function toggle(User $user, Model $likeable): array
    {
        $likeableType = $likeable::class;
        $likeableId = $likeable->id;

        // Attempt to delete an existing like first; if none deleted, insert.
        $deleted = DB::table('blog_likes')
            ->where('likeable_type', $likeableType)
            ->where('likeable_id', $likeableId)
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted) {
            $liked = false;
        } else {
            $inserted = DB::table('blog_likes')->insertOrIgnore([
                'user_id' => $user->id,
                'likeable_id' => $likeableId,
                'likeable_type' => $likeableType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $liked = $inserted > 0;
        }

        // Return current likes count as a single aggregate query.
        $count = DB::table('blog_likes')
            ->where('likeable_type', $likeableType)
            ->where('likeable_id', $likeableId)
            ->count();

        return [
            'liked' => $liked,
            'count' => $count
        ];
    }

    /**
     * Get likes count for a model.
     */
    public function getCount(Model $likeable): int
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

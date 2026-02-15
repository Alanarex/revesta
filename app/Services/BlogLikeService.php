<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\BlogLikeRepository;
use Illuminate\Database\Eloquent\Model;

class BlogLikeService
{
    public function __construct(
        protected BlogLikeRepository $blogLikeRepository
    ) {}

    /**
     * Toggle like on a likeable model (Blog or BlogComment).
     */
    public function toggleLike(User $user, Model $likeable): array
    {
        return $this->blogLikeRepository->toggle($user, $likeable);
    }

    /**
     * Get likes count for a model.
     */
    public function getLikesCount(Model $likeable): int
    {
        return $this->blogLikeRepository->getCount($likeable);
    }

    /**
     * Check if user has liked the model.
     */
    public function hasLiked(User $user, Model $likeable): bool
    {
        return $this->blogLikeRepository->hasLiked($user, $likeable);
    }
}

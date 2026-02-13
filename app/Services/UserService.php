<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\User;
use App\Mail\EmailVerificationMail;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    public function getAllUsers(int $perPage = 20, ?string $search = null, ?string $sort = null, ?string $direction = 'asc'): LengthAwarePaginator
    {
        return $this->userRepository->getAll($perPage, $search, $sort, $direction);
    }

    public function getUserCount(): int
    {
        return $this->userRepository->count();
    }

    public function findUser(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function findUserWithRelations(int $id): ?User
    {
        return $this->userRepository->findWithRelations($id);
    }

    public function createUser(array $data): User
    {
        return $this->userRepository->create($data);
    }

    public function updateUser(User $user, array $data): User
    {
        return $this->userRepository->update($user, $data);
    }

    public function deleteUser(User $user): bool
    {
        return $this->userRepository->delete($user);
    }

    public function toggleActive(User $user): bool
    {
        if ($user->trashed()) {
            return $this->userRepository->restore($user);
        }

        return $this->userRepository->delete($user);
    }

    public function setPassword(User $user, string $password): User
    {
        // relying on model cast 'password' => 'hashed' to hash automatically
        return $this->updateUser($user, ['password' => $password]);
    }

    /**
     * Send email verification with password setup link.
     */
    public function sendEmailVerification(User $user): void
    {
        Mail::send(new EmailVerificationMail($user));
    }

    /**
     * Get simulations and recent activity for the profile page.
     *
     * @return array{simulations: Collection, recentActivity: SupportCollection}
     */
    public function getSimulationsAndRecentActivity(User $user): array
    {
        try {
            $simulations = $this->userRepository->getUserSimulations($user);
            $recentActivity = $this->buildRecentActivity($user);
        } catch (\Throwable) {
            $simulations = collect();
            $recentActivity = collect();
        }

        return ['simulations' => $simulations, 'recentActivity' => $recentActivity];
    }

    /**
     * Calculate user profile completion score.
     */
    public function calculateProfileScore(User $user): int
    {
        try {
            $allInfo = !empty($user->first_name) && !empty($user->last_name) && !empty($user->email) && !empty($user->phone);
            $hasAddress = (bool) $user->address;
            $hasSimulation = $this->userRepository->hasSimulations($user);
            $hasCommented = $this->userRepository->hasBlogComments($user);
            $hasLiked = $this->userRepository->hasBlogLikes($user);

            $scoreItems = [$allInfo, $hasAddress, $hasSimulation, $hasCommented, $hasLiked];

            return (int) (array_sum($scoreItems) / count($scoreItems) * 100);
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Build recent activity collection for the profile page.
     */
    private function buildRecentActivity(User $user): SupportCollection
    {
        $oneWeekAgo = now()->subWeek();
        $recentActivity = collect();

        $recentComments = $this->userRepository->getRecentComments($user, $oneWeekAgo);
        foreach ($recentComments as $comment) {
            $recentActivity->push([
                'type' => 'comment',
                'label' => 'Commented: '.\Illuminate\Support\Str::limit($comment->body ?? '', 80),
                'url' => $comment->blog ? route('admin.blogs.show', $comment->blog) : '#',
                'created_at' => $comment->created_at,
            ]);
        }

        $recentLikes = $this->userRepository->getRecentLikes($user, $oneWeekAgo);
        foreach ($recentLikes as $like) {
            $label = 'Liked';
            $url = '#';

            if ($like->likeable_type === Blog::class) {
                $blog = $like->likeable;
                $label .= ': '.($blog->title ?? 'Blog');
                $url = $blog ? route('admin.blogs.show', $blog) : '#';
            }

            $recentActivity->push([
                'type' => 'like',
                'label' => $label,
                'url' => $url,
                'created_at' => $like->created_at,
            ]);
        }

        $recentBookmarks = $this->userRepository->getRecentBookmarks($user, $oneWeekAgo);
        foreach ($recentBookmarks as $bookmark) {
            $recentActivity->push([
                'type' => 'bookmark',
                'label' => 'Bookmarked: '.($bookmark->blog?->title ?? 'Blog'),
                'url' => $bookmark->blog ? route('admin.blogs.show', $bookmark->blog) : '#',
                'created_at' => $bookmark->created_at,
            ]);
        }

        $recentPublished = $this->userRepository->getRecentPublishedBlogs($user, $oneWeekAgo);
        foreach ($recentPublished as $published) {
            $recentActivity->push([
                'type' => 'published',
                'label' => 'Published: '.($published->title ?? 'Blog'),
                'url' => route('admin.blogs.show', $published),
                'created_at' => $published->created_at,
            ]);
        }

        $recentSimulations = $this->userRepository->getRecentSimulations($user, $oneWeekAgo);
        foreach ($recentSimulations as $simulation) {
            $recentActivity->push([
                'type' => 'simulation',
                'label' => 'Simulation: '.($simulation->title ?? 'Simulation'),
                'url' => '#',
                'created_at' => $simulation->created_at,
            ]);
        }

        return $recentActivity->sortByDesc('created_at')->values();
    }
}

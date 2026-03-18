<?php

namespace App\Services;

use App\Mail\EmailVerificationMail;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $user = $this->updateUser($user, ['password' => $password, 'email_verified_at' => null]);

        return $user;
    }

    /**
     * Send email verification with password setup link.
     */
    public function sendEmailVerification(User $user): void
    {
        Mail::send(new EmailVerificationMail($user));
    }

    /**
     * Format user data for API list response.
     */
    public function formatUserForList(User $user, bool $isAdmin = false): array
    {
        $actions = [];

        if ($isAdmin) {
            $actions[] = [
                'type' => 'edit',
                'label' => 'Modifier',
                'icon' => 'fa-edit',
                'route' => route('admin.users.show', $user),
                'class' => '',
            ];
            $actions[] = [
                'type' => 'delete',
                'label' => 'Supprimer',
                'icon' => 'fa-trash',
                'route' => route('admin.users.destroy', $user),
                'needs_confirm' => true,
                'confirm_message' => "Êtes-vous sûr de vouloir supprimer {$user->full_name} ?",
                'class' => 'text-danger',
            ];
        } else {
            $actions[] = [
                'type' => 'show',
                'label' => 'Voir',
                'icon' => 'fa-eye',
                'route' => route('admin.users.show', $user),
                'class' => '',
            ];
        }

        return [
            'id' => $user->id,
            'label' => $user->full_name ?? '-',
            'full_name' => $user->full_name ?? '-',
            'email' => $user->email ?? '-',
            'phone' => $user->phone ?? '-',
            'role' => $user->role?->name ?? '-',
            'city' => $user->city ?? ($user->address?->city ?? ''),
            'postal_code' => $user->postal_code ?? ($user->address?->postal_code ?? ''),
            'actions' => $actions,
        ];
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
            $allInfo = ! empty($user->first_name) && ! empty($user->last_name) && ! empty($user->email) && ! empty($user->phone);
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
            $blogTitle = $comment->blog?->title ?? 'Blog';
            $snippet = Str::limit($comment->content ?? '', 80);

            $recentActivity->push([
                'type' => 'comment',
                'label' => 'Commenté sur '.$blogTitle.' : '.($snippet ?: '(pas de message)'),
                'url' => $comment->blog ? route('admin.blogs.show', $comment->blog) : '#',
                'created_at' => $comment->created_at,
            ]);
        }

        $recentLikes = $this->userRepository->getRecentLikes($user, $oneWeekAgo);
        foreach ($recentLikes as $like) {
            $label = 'A aimé';
            $url = '#';
            $likeable = $like->likeable;

            if ($likeable) {
                // Direct blog like
                if ($likeable instanceof Blog) {
                    $label .= ': '.($likeable->title ?? 'Blog');
                    $url = $likeable ? route('admin.blogs.show', $likeable) : '#';
                }

                // Like on a comment or other entity that references a blog
                elseif ($likeable instanceof BlogComment && $likeable->blog) {
                    $label .= ': '.($likeable->blog->title ?? 'Blog');
                    $url = $likeable->blog ? route('admin.blogs.show', $likeable->blog) : '#';
                }

                // Fallback: use title property if present
                elseif (isset($likeable->title)) {
                    $label .= ': '.($likeable->title);
                }
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
                'label' => 'Ajouté aux favoris : '.($bookmark->blog?->title ?? 'Blog'),
                'url' => $bookmark->blog ? route('admin.blogs.show', $bookmark->blog) : '#',
                'created_at' => $bookmark->created_at,
            ]);
        }

        $recentPublished = $this->userRepository->getRecentPublishedBlogs($user, $oneWeekAgo);
        foreach ($recentPublished as $published) {
            $recentActivity->push([
                'type' => 'published',
                'label' => 'Publié : '.($published->title ?? 'Blog'),
                'url' => route('admin.blogs.show', $published),
                'created_at' => $published->created_at,
            ]);
        }

        $recentSimulations = $this->userRepository->getRecentSimulations($user, $oneWeekAgo);
        foreach ($recentSimulations as $simulation) {
            $simulationLabel = $simulation->ad?->titre ?: ('Simulation #'.$simulation->id);

            $recentActivity->push([
                'type' => 'simulation',
                'label' => 'Simulation : '.$simulationLabel,
                'url' => '#',
                'created_at' => $simulation->created_at,
            ]);
        }

        return $recentActivity->sortByDesc('created_at')->values();
    }
}

<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Models\BlogLike;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function update(User $user, array $attributes): User
    {
        $user->fill($attributes);
        $user->save();

        return $user;
    }

    public function findByEmail(?string $email): ?User
    {
        if (empty($email)) {
            return null;
        }

        return User::where('email', $email)->first();
    }

    /**
     * Get all users with optional filtering and pagination.
     */
    public function getAll(int $perPage = 20, ?string $search = null, ?string $sortColumn = null, ?string $sortDirection = 'asc')
    {
        // join addresses so we can search/sort on city and postal_code directly
        $query = User::select([
            'users.id',
            'users.first_name',
            'users.last_name',
            'users.email',
            'users.phone',
            'users.role_id',
            'users.address_id',
            'users.created_at',
            'users.updated_at',
            'addresses.city as city',
            'addresses.postal_code as postal_code',
        ])
            ->leftJoin('addresses', 'users.address_id', '=', 'addresses.id')
            ->with('role');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('users.id', 'like', '%'.$search.'%')
                    ->orWhere('users.first_name', 'like', '%'.$search.'%')
                    ->orWhere('users.last_name', 'like', '%'.$search.'%')
                    ->orWhere('users.email', 'like', '%'.$search.'%')
                    ->orWhere('users.phone', 'like', '%'.$search.'%')
                    ->orWhere('addresses.city', 'like', '%'.$search.'%')
                    ->orWhere('addresses.postal_code', 'like', '%'.$search.'%');
            });
        }

        $validColumns = ['id', 'first_name', 'last_name', 'email', 'phone', 'city', 'postal_code', 'created_at', 'updated_at'];
        $sortColumn = in_array($sortColumn, $validColumns) ? $sortColumn : 'last_name';
        $sortDirection = strtolower($sortDirection) === 'desc' ? 'desc' : 'asc';

        return $query->orderBy($sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    public function find(int $id): ?User
    {
        return User::with('role')->find($id);
    }

    /**
     * Find user with common relations used by the admin show page.
     */
    public function findWithRelations(int $id): ?User
    {
        return User::with(['role', 'address', 'blogBookmarks', 'blogs', 'blogComments', 'simulations'])->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function restore(User $user): bool
    {
        if (method_exists($user, 'restore')) {
            return (bool) $user->restore();
        }

        return false;
    }

    public function count(): int
    {
        return User::count();
    }

    /**
     * Get all simulations for a user (most recent first).
     */
    public function getUserSimulations(User $user): Collection
    {
        return $user->simulations()->orderByDesc('created_at')->get();
    }

    /**
     * Get recent blog comments for a user.
     */
    public function getRecentComments(User $user, Carbon $since): Collection
    {
        return $user->blogComments()
            ->where('created_at', '>=', $since)
            ->with('blog')
            ->latest()
            ->get();
    }

    /**
     * Get recent likes for a user.
     */
    public function getRecentLikes(User $user, Carbon $since): Collection
    {
        return BlogLike::where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->with('likeable')
            ->latest()
            ->get();
    }

    /**
     * Get recent bookmarks for a user.
     */
    public function getRecentBookmarks(User $user, Carbon $since): Collection
    {
        return $user->blogBookmarks()
            ->where('created_at', '>=', $since)
            ->with('blog')
            ->latest()
            ->get();
    }

    /**
     * Get recently published blogs for a user.
     */
    public function getRecentPublishedBlogs(User $user, Carbon $since): Collection
    {
        return Blog::where('user_id', $user->id)
            ->where('status', Blog::PUBLISHED)
            ->where('created_at', '>=', $since)
            ->latest()
            ->get();
    }

    /**
     * Get recent simulations for a user.
     */
    public function getRecentSimulations(User $user, Carbon $since): Collection
    {
        return $user->simulations()
            ->where('created_at', '>=', $since)
            ->latest()
            ->get();
    }

    /**
     * Check if user has simulations.
     */
    public function hasSimulations(User $user): bool
    {
        return $user->simulations()->exists();
    }

    /**
     * Check if user has blog comments.
     */
    public function hasBlogComments(User $user): bool
    {
        return $user->blogComments()->exists();
    }

    /**
     * Check if user has blog likes.
     */
    public function hasBlogLikes(User $user): bool
    {
        return BlogLike::where('user_id', $user->id)->exists();
    }
}

<?php

namespace App\Repositories;

use App\Models\Blog;
use App\Models\BlogLike;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class UserRepository
{
    /**
     * Upsert a user from a simulation API payload.
     *
     * @return array{user: User, created: bool}
     */
    public function upsertFromPayload(array $payload): array
    {
        $email = mb_strtolower(trim((string) Arr::get($payload, 'email')));
        $existing = User::query()->where('email', $email)->first();
        $isNew = $existing === null;

        $attributes = [
            'first_name' => Arr::get($payload, 'prenom') ?: Arr::get($payload, 'first_name') ?: 'Utilisateur',
            'last_name' => Arr::get($payload, 'nom') ?: Arr::get($payload, 'last_name') ?: '-',
            'email' => $email,
            'telephone' => Arr::get($payload, 'telephone'),
            'phone' => Arr::get($payload, 'telephone'),
            'code_postal' => Arr::get($payload, 'code_postal'),
            'revenus' => Arr::get($payload, 'revenus'),
            'nombre_personnes' => Arr::get($payload, 'nombre_personnes'),
            'residence_principale' => Arr::get($payload, 'residence_principale'),
            'budget_achat' => Arr::get($payload, 'budget_achat'),
            'surface_logement' => Arr::get($payload, 'surface_logement'),
            'budget_travaux' => Arr::get($payload, 'budget_travaux'),
            'taxe_fonciere' => Arr::get($payload, 'taxe_fonciere'),
            'condition_depenses' => Arr::get($payload, 'condition_depenses'),
            'notifications_aides' => Arr::get($payload, 'notifications_aides', true),
            'notifications_prix' => Arr::get($payload, 'notifications_prix', true),
            'accept_analytics' => Arr::get($payload, 'accept_analytics', true),
            'user_status_id' => $this->resolveUserStatusId(Arr::get($payload, 'statut')),
            'dpe_actuel_id' => $this->resolveDpeClassId(Arr::get($payload, 'dpe_actuel')),
            'dpe_vise_id' => $this->resolveDpeClassId(Arr::get($payload, 'dpe_vise')),
            'construction_period_id' => $this->resolveConstructionPeriodId(Arr::get($payload, 'periode_construction')),
            'housing_type_id' => $this->resolveHousingTypeId(Arr::get($payload, 'type_logement')),
            'energy_gain_target_id' => $this->resolveEnergyGainTargetId(Arr::get($payload, 'gain_energetique')),
            'aid_path_id' => $this->resolveAidPathId(Arr::get($payload, 'parcours_aide')),
        ];

        if ($isNew) {
            $attributes['email_verified_at'] = null;
            $attributes['password'] = Str::random(40);
        }

        $user = User::query()->updateOrCreate(['email' => $email], array_filter($attributes, fn($value) => $value !== null));

        return ['user' => $user, 'created' => $isNew];
    }

    private function resolveUserStatusId(mixed $value): ?string
    {
        $input = mb_strtolower(trim((string) $value));
        if ($input === '') {
            return null;
        }

        $aliases = (array) config('users.simulation_status_aliases', []);
        return $aliases[$input] ?? null;
    }

    private function resolveDpeClassId(mixed $value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        $upper = mb_strtoupper($raw);
        $numericMap = (array) config('housing.dpe_numeric_map', []);
        return $numericMap[$raw] ?? $upper;
    }

    private function resolveConstructionPeriodId(mixed $value): ?string
    {
        $input = mb_strtolower(trim((string) $value));
        if ($input === '') {
            return null;
        }

        $aliases = (array) config('housing.construction_period_aliases', []);
        return $aliases[$input] ?? null;
    }

    private function resolveHousingTypeId(mixed $value): ?string
    {
        $input = mb_strtolower(trim((string) $value));
        if ($input === '' || $input === 'indifferent') {
            return null;
        }

        return $input;
    }

    private function resolveEnergyGainTargetId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $classes = (int) $value;
        if ($classes <= 0) {
            return null;
        }

        return $classes;
    }

    private function resolveAidPathId(mixed $value): ?string
    {
        $input = mb_strtolower(trim((string) $value));
        if ($input === '') {
            return null;
        }

        $aliases = (array) config('aid.parcours_aide_aliases', []);
        return $aliases[$input] ?? null;
    }

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
                $q->where('users.id', 'like', '%' . $search . '%')
                    ->orWhere('users.first_name', 'like', '%' . $search . '%')
                    ->orWhere('users.last_name', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%')
                    ->orWhere('users.phone', 'like', '%' . $search . '%')
                    ->orWhere('addresses.city', 'like', '%' . $search . '%')
                    ->orWhere('addresses.postal_code', 'like', '%' . $search . '%');
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

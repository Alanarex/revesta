<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}

<?php

namespace App\Services;

use App\Repositories\RoleRepository;

class RoleService
{
    public function __construct(protected RoleRepository $roleRepository) {}

    /**
     * Get roles as a value => label map for select inputs.
     *
     * @return array<int, string>
     */
    public function getRolesForSelect(): array
    {
        return $this->roleRepository->getForSelect();
    }
}

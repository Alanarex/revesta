<?php

namespace App\Repositories;

use App\Models\Role;

class RoleRepository
{
    /**
     * Get roles as a value => label map for select inputs.
     *
     * @return array<int, string>
     */
    public function getForSelect(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get(['id', 'name', 'display_name'])
            ->mapWithKeys(function (Role $role) {
                $label = $role->display_name ?: $role->name;

                return [$role->id => $label];
            })
            ->all();
    }
}

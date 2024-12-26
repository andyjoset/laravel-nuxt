<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function index(User $user): ?bool
    {
        return $user->hasPermissionTo('roles.index') ? true : null;
    }

    public function store(User $user): ?bool
    {
        return $user->hasPermissionTo('roles.store') ? true : null;
    }

    public function update(User $user, Role $role): ?bool
    {
        if (in_array($role->name, ['Super Admin'])) {
            return false;
        }

        return $user->hasPermissionTo('roles.update') ? true : null;
    }

    public function delete(User $user, Role $role): ?bool
    {
        if (in_array($role->name, ['Super Admin'])) {
            return false;
        }

        return $user->hasPermissionTo('roles.delete') ? true : null;
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function index(User $user)
    {
        return $user->hasPermissionTo('users.index') ? true : null;
    }

    public function store(User $user)
    {
        return $user->hasPermissionTo('users.store') ? true : null;
    }

    public function update(User $user, User $model)
    {
        if ($model->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('users.update') ? true : null;
    }

    public function delete(User $user, User $model)
    {
        if ($model->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('users.delete') ? true : null;
    }

    public function toggle(User $user, User $model)
    {
        if ($model->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('users.toggle') ? true : null;
    }

    public function assignRoles(User $user)
    {
        return $user->hasPermissionTo('users.assign-role') ? true : null;
    }
}

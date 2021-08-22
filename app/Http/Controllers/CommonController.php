<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Resources\Role as RoleResource;
use App\Http\Resources\Permission as PermissionResource;

class CommonController extends Controller
{
    /**
     * Display a listing of all roles to be used in assigning user roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function roles(Request $request)
    {
        $this->authorize('assignRoles', User::class);

        return RoleResource::collection(
            Role::select(['id', 'name'])->orderBy('name')->paginate()
        );
    }

    /**
     * Display a listing of all permissions to be used in roles permissions assignment.
     *
     * @return \Illuminate\Http\Response
     */
    public function permissions(Request $request)
    {
        $this->authorize('index', Role::class);

        return PermissionResource::collection(
            Permission::select(['id', 'module', 'description'])->orderBy('name')->get()
        );
    }
}

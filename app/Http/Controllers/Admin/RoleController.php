<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\Role as RoleResource;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', Role::class);

        return RoleResource::collection(
            Role::with('permissions:id,name')->orderBy('name')->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|unique:roles',
            'permissions' => 'required|array|min:1|exists:permissions,id'
        ]);

        $this->authorize('store', Role::class);

        $role = Role::create([
            'name' => $request->input('name'),
            'guard_name' => 'web',
        ]);

        $role->givePermissionTo($request->only('permissions'));

        return new RoleResource($role->load('permissions:id,name'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array|min:1|exists:permissions,id'
        ]);

        $this->authorize('update', $role);

        $role->update($request->only('name'));

        $role->syncPermissions($request->only('permissions'));

        return new RoleResource($role->load('permissions:id,name'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->json(['status' => 'OK']);
    }
}

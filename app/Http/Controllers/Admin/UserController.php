<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Notifications\UserAccountGenerated;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('index', User::class);

        $search = $request->input('s');

        return UserResource::collection(
            User::with('roles:id,name')
                ->orderByDesc('created_at')
                ->when(!empty($search), function ($query) use ($search) {
                    $query->where(
                        fn ($q) =>
                        $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                    );
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('active', (bool) json_decode($request->input('status')));
                })
                ->paginate(10)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $this->authorize('store', User::class);

        $user = User::create($data = $request->validated());

        if (!empty($data['role_id'])) {
            $user->assignRole($data['role_id']);
        }

        $user->notify(new UserAccountGenerated($data['plain_password']));

        return new UserResource($user->load('roles:id,name'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update($data = $request->validated());

        if (array_key_exists('role_id', $data)) {
            $user->syncRoles($data['role_id']);
        }

        return new UserResource($user->load('roles:id,name'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        return response()->json(['status' => 'OK']);
    }

    /**
     * Toggle active field of the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function toggle(User $user)
    {
        $this->authorize('toggle', $user);

        $user->update([
            'active' => !$user->active
        ]);

        return response()->json(['active' => $user->active]);
    }
}

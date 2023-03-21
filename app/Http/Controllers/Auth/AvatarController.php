<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    /**
     * Updates the user avatar.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $this->deleteOldAvatar($user = $request->user());

        $user->update([
            'avatar' => $request->file('avatar')->store('avatars')
        ]);

        return response()->json(['photo_url' => $user->photo_url]);
    }

    /**
     * Restore the user avatar to default value.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $this->deleteOldAvatar($user = $request->user());

        $user->update([
            'avatar' => User::DEFAULT_AVATAR_PATH
        ]);

        return response()->json(['photo_url' => $user->photo_url]);
    }

    /**
     * Delete from storage the current user avatar if it isn't the default one.
     */
    protected function deleteOldAvatar(User $user): void
    {
        if ($user->avatar !== User::DEFAULT_AVATAR_PATH) {
            Storage::delete($user->avatar);
        }
    }
}

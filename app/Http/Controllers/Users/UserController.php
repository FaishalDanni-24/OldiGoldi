<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /** Return the authenticated user's profile. */
    public function me(Request $request)
    {
        return response()->json(['data' => $request->user()]);
    }

    /** Update basic profile fields; role and password are intentionally excluded. */
    public function updateMe(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:120'],
            'email' => [
                'sometimes', 'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:30'],
        ]);

        $user->update($data);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => $user->fresh(),
        ]);
    }
}

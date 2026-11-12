<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()]);
    }

    public function updateMe(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:120'],
            'email' => ['sometimes', 'email', 'max:150', 'unique:users,email,' . $user->user_id . ',user_id'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:30'],
        ]);

        $user->update($validated);

        return response()->json(['data' => $user->fresh()]);
    }

    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->filled('role_id'), fn ($q) => $q->where('role_id', $request->integer('role_id')))
            ->when($request->filled('account_status'), fn ($q) => $q->where('account_status', $request->string('account_status')))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->paginate($request->integer('per_page', 15));

        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->load('role')]);
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'account_status' => ['required', 'in:active,pending_verification,blocked'],
        ]);

        $user->update($validated);

        return response()->json(['data' => $user->fresh()]);
    }

    public function updateRole(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'role_id' => ['required', 'integer', 'exists:roles,role_id'],
        ]);

        $user->update($validated);

        return response()->json(['data' => $user->fresh('role')]);
    }
}

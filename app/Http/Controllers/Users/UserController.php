<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateProfileRequest;
use App\Http\Resources\Users\UserResource;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => new UserResource($request->user())]);
    }

    public function updateMe(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json(['data' => new UserResource($user->fresh())]);
    }

    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->filled('role_id'), fn ($q) => $q->where('role_id', $request->integer('role_id')))
            ->when($request->filled('account_status'), fn ($q) => $q->where('account_status', $request->string('account_status')))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->paginate($request->integer('per_page', 15));

        return response()->json(UserResource::collection($users));
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => new UserResource($user->load('role'))]);
    }

    public function updateStatus(\App\Http\Requests\Users\UpdateUserStatusRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return response()->json(['data' => new UserResource($user->fresh())]);
    }

    public function updateRole(\App\Http\Requests\Users\UpdateUserRoleRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return response()->json(['data' => new UserResource($user->fresh('role'))]);
    }
}

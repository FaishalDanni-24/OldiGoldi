<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\RoleResource;
use App\Models\Users\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => RoleResource::collection(Role::all())]);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json(['data' => new RoleResource($role)]);
    }

    public function store(\App\Http\Requests\Users\StoreRoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());

        return response()->json(['data' => new RoleResource($role)], 201);
    }

    public function update(\App\Http\Requests\Users\UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role->update($request->validated());

        return response()->json(['data' => new RoleResource($role->fresh())]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted.']);
    }
}

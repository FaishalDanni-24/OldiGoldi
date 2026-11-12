<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Role::all()]);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json(['data' => $role]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:30', 'unique:roles,role_name'],
        ]);

        $role = Role::create($validated);

        return response()->json(['data' => $role], 201);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'role_name' => ['required', 'string', 'max:30', 'unique:roles,role_name,' . $role->role_id . ',role_id'],
        ]);

        $role->update($validated);

        return response()->json(['data' => $role->fresh()]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $role->delete();

        return response()->json(['message' => 'Role deleted.']);
    }
}

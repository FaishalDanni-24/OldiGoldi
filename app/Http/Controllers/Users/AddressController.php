<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\Address;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->get();

        return response()->json(['data' => $addresses]);
    }

    public function show(Address $address): JsonResponse
    {
        $this->authorizeOwner($address, request()->user());

        return response()->json(['data' => $address]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address_label' => ['nullable', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:120'],
            'recipient_phone' => ['required', 'string', 'max:30'],
            'address_line' => ['required', 'string', 'max:500'],
            'district' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:15'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        $validated['user_id'] = $request->user()->user_id;

        if (! empty($validated['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = Address::create($validated);

        return response()->json(['data' => $address], 201);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        $this->authorizeOwner($address, $request->user());

        $validated = $request->validate([
            'address_label' => ['sometimes', 'nullable', 'string', 'max:50'],
            'recipient_name' => ['sometimes', 'string', 'max:120'],
            'recipient_phone' => ['sometimes', 'string', 'max:30'],
            'address_line' => ['sometimes', 'string', 'max:500'],
            'district' => ['sometimes', 'nullable', 'string', 'max:100'],
            'city' => ['sometimes', 'string', 'max:100'],
            'province' => ['sometimes', 'nullable', 'string', 'max:100'],
            'postal_code' => ['sometimes', 'nullable', 'string', 'max:15'],
            'is_default' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('is_default', $validated) && $validated['is_default']) {
            $request->user()->addresses()->where('address_id', '!=', $address->address_id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json(['data' => $address->fresh()]);
    }

    public function destroy(Address $address): JsonResponse
    {
        $this->authorizeOwner($address, request()->user());

        $address->delete();

        return response()->json(['message' => 'Address deleted.']);
    }

    public function setDefault(Address $address): JsonResponse
    {
        $user = request()->user();
        $this->authorizeOwner($address, $user);

        $user->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json(['data' => $address->fresh()]);
    }

    protected function authorizeOwner(Address $address, ?User $user): void
    {
        if (! $user || $address->user_id !== $user->user_id) {
            abort(403, 'Forbidden.');
        }
    }
}

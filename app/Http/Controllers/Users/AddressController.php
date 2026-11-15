<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreAddressRequest;
use App\Http\Requests\Users\UpdateAddressRequest;
use App\Http\Resources\Users\AddressResource;
use App\Models\Users\Address;
use App\Models\Users\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => AddressResource::collection($request->user()->addresses()->get())]);
    }

    public function show(Address $address): JsonResponse
    {
        $this->authorizeOwner($address, request()->user());

        return response()->json(['data' => new AddressResource($address)]);
    }

    public function store(StoreAddressRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->user_id;

        if (! empty($validated['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = Address::create($validated);

        return response()->json(['data' => new AddressResource($address)], 201);
    }

    public function update(UpdateAddressRequest $request, Address $address): JsonResponse
    {
        $this->authorizeOwner($address, $request->user());

        $validated = $request->validated();

        if (array_key_exists('is_default', $validated) && $validated['is_default']) {
            $request->user()->addresses()->where('address_id', '!=', $address->address_id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json(['data' => new AddressResource($address->fresh())]);
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

        return response()->json(['data' => new AddressResource($address->fresh())]);
    }

    protected function authorizeOwner(Address $address, ?User $user): void
    {
        if (! $user || $address->user_id !== $user->user_id) {
            abort(403, 'Forbidden.');
        }
    }
}

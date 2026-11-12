<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products\Product;
use App\Models\Products\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->images]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'image' => ['required_without:image_url', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_url' => ['required_without:image', 'nullable', 'string', 'max:1000'],
            'is_primary' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $imageUrl = $validated['image_url'] ?? null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        }

        if (! empty($validated['is_primary'])) {
            $product->images()->update(['is_primary' => false]);
        }

        $image = ProductImage::create([
            'product_id' => $product->product_id,
            'image_url' => $imageUrl,
            'is_primary' => $validated['is_primary'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json(['data' => $image], 201);
    }

    public function update(Request $request, ProductImage $image): JsonResponse
    {
        $validated = $request->validate([
            'is_primary' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        if (array_key_exists('is_primary', $validated) && $validated['is_primary']) {
            ProductImage::where('product_id', $image->product_id)
                ->where('image_id', '!=', $image->image_id)
                ->update(['is_primary' => false]);
        }

        $image->update($validated);

        return response()->json(['data' => $image->fresh()]);
    }

    public function destroy(ProductImage $image): JsonResponse
    {
        $image->delete();

        return response()->json(['message' => 'Image deleted.']);
    }
}

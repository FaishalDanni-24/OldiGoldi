<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductImageRequest;
use App\Http\Requests\Products\UpdateProductImageRequest;
use App\Http\Resources\Products\ProductImageResource;
use App\Models\Products\Product;
use App\Models\Products\ProductImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function index(Product $product): JsonResponse
    {
        return response()->json(['data' => ProductImageResource::collection($product->images)]);
    }

    public function store(StoreProductImageRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

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

        return response()->json(['data' => new ProductImageResource($image)], 201);
    }

    public function update(UpdateProductImageRequest $request, ProductImage $image): JsonResponse
    {
        $validated = $request->validated();

        if (array_key_exists('is_primary', $validated) && $validated['is_primary']) {
            ProductImage::where('product_id', $image->product_id)
                ->where('image_id', '!=', $image->image_id)
                ->update(['is_primary' => false]);
        }

        $image->update($validated);

        return response()->json(['data' => new ProductImageResource($image->fresh())]);
    }

    public function destroy(ProductImage $image): JsonResponse
    {
        $image->delete();

        return response()->json(['message' => 'Image deleted.']);
    }
}

<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Http\Resources\Products\ProductResource;
use App\Models\Products\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')))
            ->when($request->filled('condition_grade'), fn ($q) => $q->where('condition_grade', $request->string('condition_grade')))
            ->when($request->filled('product_status'), fn ($q) => $q->where('product_status', $request->string('product_status')))
            ->when($request->filled('brand'), fn ($q) => $q->where('brand', 'like', '%' . $request->string('brand') . '%'))
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->numeric('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->numeric('max_price')))
            ->when($request->filled('search'), fn ($q) => $q->where('product_name', 'like', '%' . $request->string('search') . '%'))
            ->when($request->boolean('in_stock'), fn ($q) => $q->where('stock', '>', 0))
            ->orderBy($request->string('sort', 'created_at'), $request->string('dir', 'desc'))
            ->paginate($request->integer('per_page', 15));

        return response()->json(ProductResource::collection($products));
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => new ProductResource($product->load(['category', 'seller', 'images', 'primaryImage']))]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()?->user_id;
        $validated['stock'] = $validated['stock'] ?? 1;
        $validated['product_status'] = $validated['product_status'] ?? Product::STATUS_DRAFT;

        $product = Product::create($validated);

        return response()->json(['data' => new ProductResource($product->load(['category', 'images']))], 201);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());

        return response()->json(['data' => new ProductResource($product->fresh(['category', 'images']))]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted.']);
    }

    public function toggleStatus(Product $product): JsonResponse
    {
        $product->product_status = $product->product_status === Product::STATUS_ACTIVE
            ? Product::STATUS_INACTIVE
            : Product::STATUS_ACTIVE;
        $product->save();

        return response()->json(['data' => new ProductResource($product->fresh())]);
    }
}

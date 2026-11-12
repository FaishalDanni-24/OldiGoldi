<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
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

        return response()->json($products);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->load(['category', 'seller', 'images'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,category_id'],
            'sku' => ['required', 'string', 'max:60', 'unique:products,sku'],
            'product_name' => ['required', 'string', 'max:180'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:120'],
            'product_description' => ['nullable', 'string'],
            'condition_grade' => ['required', 'in:like_new,good,fair,for_parts'],
            'condition_notes' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'product_status' => ['sometimes', 'in:draft,active,sold_out,inactive'],
        ]);

        $validated['user_id'] = $request->user()?->user_id;
        $validated['stock'] = $validated['stock'] ?? 1;
        $validated['product_status'] = $validated['product_status'] ?? Product::STATUS_DRAFT;

        $product = Product::create($validated);

        return response()->json(['data' => $product->load(['category', 'images'])], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['sometimes', 'integer', 'exists:categories,category_id'],
            'sku' => ['sometimes', 'string', 'max:60', 'unique:products,sku,' . $product->product_id . ',product_id'],
            'product_name' => ['sometimes', 'string', 'max:180'],
            'brand' => ['sometimes', 'nullable', 'string', 'max:100'],
            'model' => ['sometimes', 'nullable', 'string', 'max:120'],
            'product_description' => ['sometimes', 'nullable', 'string'],
            'condition_grade' => ['sometimes', 'in:like_new,good,fair,for_parts'],
            'condition_notes' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'product_status' => ['sometimes', 'in:draft,active,sold_out,inactive'],
        ]);

        $product->update($validated);

        return response()->json(['data' => $product->fresh(['category', 'images'])]);
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

        return response()->json(['data' => $product->fresh()]);
    }
}

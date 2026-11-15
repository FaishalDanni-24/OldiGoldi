<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\StoreCategoryRequest;
use App\Http\Requests\Products\UpdateCategoryRequest;
use App\Http\Resources\Products\CategoryResource;
use App\Models\Products\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Category::query()
            ->when($request->boolean('active_only'), fn ($q) => $q->where('is_active', true))
            ->orderBy('category_name')
            ->get();

        return response()->json(['data' => CategoryResource::collection($categories)]);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json(['data' => new CategoryResource($category->load('products'))]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['category_name']);
        }

        $category = Category::create($validated);

        return response()->json(['data' => new CategoryResource($category)], 201);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json(['data' => new CategoryResource($category->fresh())]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }
}

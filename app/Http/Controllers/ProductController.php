<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        try {

            $products = Product::with('category')->paginate(6);

            return response()->json([
                'status' => 'success',
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'quantite' => 'required|integer|min:0',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!empty($request['image'])) {
                $photoData = base64_decode($request['image']);
                $fileName = 'products/' . uniqid() . '.jpg';
                Storage::disk('public')->put($fileName, $photoData);

                $product = new Product();
                $product['image'] = $fileName;
                $product['name'] = $request['name'];
                $product['description'] = $request['description'];
                $product['price'] = $request['price'];
                $product['category_id'] = $request['category_id'];
                $product['average_rating'] = $request['average_rating'];
                $product->save();

            }

            $data = $request->all();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $data['image'] = $path;
            }

            $product = Product::create($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = Product::with('category')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, Product $product)
    {
        try {

            $validated = $request->validate([
                'name' => 'nullable|string',
                'description' => 'sometimes|string|max:255',
                'price' => 'sometimes|numeric',
                'category_id' => 'sometimes|numeric',
                'quantite' => 'sometimes|numeric',
                'image' => 'nullable|string',
            ]);

            if (!empty($validated['image'])) {
                $photoData = base64_decode($validated['image']);
                $fileName = 'products/' . uniqid() . '.jpg';
                Storage::disk('public')->put($fileName, $photoData);
                $validated['image'] = $fileName;
            }

            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }

                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = $path;
            }

            $product->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully',
                'product' => $product,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update product',
                'error' => $e->getMessage(),
            ], 500);
        }

    }



    public function destroy(int $id): JsonResponse
    {
        try {
            $product = Product::findOrFail($id);

            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Product deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:2',
                'category' => 'nullable|exists:categories,id',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'sort_by' => 'nullable|in:price_asc,price_desc,name_asc,name_desc'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Product::query();

            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->query}%")
                  ->orWhere('description', 'LIKE', "%{$request->query}%");
            });

            if ($request->category) {
                $query->where('category_id', $request->category);
            }

            if ($request->min_price) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->max_price) {
                $query->where('price', '<=', $request->max_price);
            }

            switch ($request->sort_by) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
            }

            $products = $query->with('category')->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function filterByCategory(string $category): JsonResponse
    {
        try {
            $products = Product::with('category')
                ->whereHas('category', function($query) use ($category) {
                    $query->where('name', $category);
                })
                ->paginate(10);

            if ($products->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No products found in this category'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $products
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch products by category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

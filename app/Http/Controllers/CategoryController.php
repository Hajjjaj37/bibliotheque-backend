<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $categories = Category::with('products')->get();

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories',
                'description' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = Category::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);



        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $category = Category::with('products')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            if (!Auth::user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255|unique:categories,name,' . $id,
                'description' => 'sometimes|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = Category::findOrFail($id);
            $category->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully',
                'data' => $category
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    try {
        // Récupérer l'entrée avec l'ID spécifié
        $category = Category::find($id);

        // Vérifier si l'entrée existe
        if (!$category) {
            return response()->json([
                'message' => 'Category not found.'
            ], 404);
        }

        // Supprimer l'entrée
        $category->delete();

        // Retourner une réponse de succès
        return response()->json([
            'message' => 'Category deleted successfully.'
        ], 200);
    } catch (\Exception $e) {
        // En cas d'erreur, retourner une réponse appropriée
        return response()->json([
            'message' => 'An error occurred while deleting the category.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}

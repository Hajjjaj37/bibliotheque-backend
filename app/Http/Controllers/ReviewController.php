<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index(int $productId): JsonResponse
    {
        try {
            $reviews = Review::with('user')
                ->where('product_id', $productId)
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $reviews
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'rating' => 'required|integer|between:1,5',
                'comment' => 'required|string|min:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $existingReview = Review::where('user_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already reviewed this product'
                ], 400);
            }

            $review = Review::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);

            $product = Product::findOrFail($request->product_id);
            $avgRating = Review::where('product_id', $request->product_id)
                ->avg('rating');
            $product->update(['average_rating' => $avgRating]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review created successfully',
                'data' => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|integer|between:1,5',
                'comment' => 'sometimes|string|min:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $review = Review::findOrFail($id);

            if ($review->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $review->update($request->all());

            $avgRating = Review::where('product_id', $review->product_id)
                ->avg('rating');
            $review->product->update(['average_rating' => $avgRating]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review updated successfully',
                'data' => $review
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $review = Review::findOrFail($id);

            if (!Auth::user()->isAdmin() && $review->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $review->delete();

            $avgRating = Review::where('product_id', $review->product_id)
                ->avg('rating');
            $review->product->update(['average_rating' => $avgRating]);

            return response()->json([
                'status' => 'success',
                'message' => 'Review deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete review',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
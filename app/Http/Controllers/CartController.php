<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $cart = Cart::with(['items.product'])
                ->where('user_id', Auth::id())
                ->firstOrCreate(['user_id' => Auth::id()]);

            $total = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'cart' => $cart,
                    'total' => $total
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function addItem(Request $request): JsonResponse
    {
        try {

            $product = Product::findOrFail($request->product_id);

            // Check if product is in stock
            if ($product->stock < $request->quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough stock available'
                ], 400);
            }

            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

            // Check if product already exists in cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($cartItem) {
                // Update quantity if product already exists
                $newQuantity = $cartItem->quantity + $request->quantity;

                if ($newQuantity > $product->stock) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Not enough stock available'
                    ], 400);
                }

                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                // Create new cart item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity

                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Product added to cart successfully',
                'data' => $cartItem
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add item to cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cartItem = CartItem::findOrFail($itemId);

            // Verify cart belongs to authenticated user
            if ($cartItem->cart->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Check stock availability
            if ($cartItem->product->stock < $request->quantity) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not enough stock available'
                ], 400);
            }

            $cartItem->update(['quantity' => $request->quantity]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cart item updated successfully',
                'data' => $cartItem
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update cart item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeItem(int $itemId): JsonResponse
    {
        try {
            $cartItem = CartItem::findOrFail($itemId);

            // Verify cart belongs to authenticated user
            if ($cartItem->cart->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $cartItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item removed from cart successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to remove item from cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clear(): JsonResponse
    {
        try {
            $cart = Cart::where('user_id', Auth::id())->first();

            if ($cart) {
                $cart->items()->delete();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cart cleared successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cart',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

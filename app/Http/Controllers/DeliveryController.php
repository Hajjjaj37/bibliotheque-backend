<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $deliveries = Delivery::with(['order', 'user'])
                ->when(!Auth::user()->isAdmin(), function ($query) {
                    return $query->where('user_id', Auth::id());
                })
                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $deliveries
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch deliveries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'order_id' => 'required|exists:orders,id',
                'address' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'postal_code' => 'required|string',
                'phone' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order = Order::findOrFail($request->order_id);
            
            // Verify order belongs to authenticated user or user is admin
            if (!Auth::user()->isAdmin() && $order->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $delivery = Delivery::create([
                'order_id' => $request->order_id,
                'user_id' => $order->user_id,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'status' => 'pending'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery created successfully',
                'data' => $delivery
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create delivery',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $delivery = Delivery::with(['order', 'user'])->findOrFail($id);
            
            // Verify delivery belongs to authenticated user or user is admin
            if (!Auth::user()->isAdmin() && $delivery->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => $delivery
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Delivery not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'address' => 'sometimes|string',
                'city' => 'sometimes|string',
                'state' => 'sometimes|string',
                'postal_code' => 'sometimes|string',
                'phone' => 'sometimes|string',
                'status' => 'sometimes|in:pending,processing,shipped,delivered,cancelled'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $delivery = Delivery::findOrFail($id);
            
            // Only admin can update status
            if ($request->has('status') && !Auth::user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized to update status'
                ], 403);
            }

            // Verify delivery belongs to authenticated user or user is admin
            if (!Auth::user()->isAdmin() && $delivery->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], 403);
            }

            $delivery->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Delivery updated successfully',
                'data' => $delivery
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update delivery',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string',
                'status' => 'nullable|in:pending,processing,shipped,delivered,cancelled'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Delivery::with(['order', 'user']);

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Search in address, city, state, postal_code
            $query->where(function ($q) use ($request) {
                $q->where('address', 'LIKE', "%{$request->query}%")
                    ->orWhere('city', 'LIKE', "%{$request->query}%")
                    ->orWhere('state', 'LIKE', "%{$request->query}%")
                    ->orWhere('postal_code', 'LIKE', "%{$request->query}%");
            });

            // Filter by user if not admin
            if (!Auth::user()->isAdmin()) {
                $query->where('user_id', Auth::id());
            }

            $deliveries = $query->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $deliveries
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
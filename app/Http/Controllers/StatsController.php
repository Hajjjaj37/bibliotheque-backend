<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $bestSellers = Category::with(['products' => function ($query) {
                $query->select('products.*')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->selectRaw('COUNT(order_items.id) as sales_count')
                    ->groupBy('products.id')
                    ->orderByRaw('COUNT(order_items.id) DESC')
                    ->limit(3);
            }])->get();

            $totalClients = User::where('role', 'member')->count();

            $currentOrders = Order::whereIn('status', ['pending', 'processing'])->count();

            $totalRevenue = Order::where('status', 'completed')
                ->sum('total_amount');

            $totalReviews = Review::count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'best_sellers_by_category' => $bestSellers->map(function ($category) {
                        return [
                            'category_name' => $category->name,
                            'top_products' => $category->products->map(function ($product) {
                                return [
                                    'product_name' => $product->name,
                                    'sales_count' => $product->sales_count,
                                    'price' => $product->price
                                ];
                            })
                        ];
                    }),
                    'total_clients' => $totalClients,
                    'current_orders' => $currentOrders,
                    'total_revenue' => $totalRevenue,
                    'total_reviews' => $totalReviews
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

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
                $query->select('products.id', 'products.name', 'products.price', 'products.category_id')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->selectRaw('COUNT(order_items.id) as sales_count')
                    ->groupBy('products.id', 'products.name', 'products.price', 'products.category_id')
                    ->orderByRaw('COUNT(order_items.id) DESC')
                    ->limit(3);
            }])->get();

            $totalClients = User::where('roles', 'member')->count();
            $totalUsers = User::count();
            $totalProducts = Product::count();
            $totalPayments = Order::where('payment_status', 'paid')->count();

            $currentOrders = Order::whereIn('status', ['pending', 'processing'])->count();

            $totalRevenue = Order::where('status', 'completed')
                ->sum('total_amount');

            $totalReviews = Review::count();

            // Get recent sales
            $recentSales = Order::with(['user', 'items.product'])
                ->where('status', 'completed')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'order_id' => $order->id,
                        'customer_name' => $order->user->name,
                        'amount' => $order->total_amount,
                        'date' => $order->created_at,
                        'items' => $order->items->map(function ($item) {
                            return [
                                'product_name' => $item->product->name,
                                'quantity' => $item->quantity,
                                'price' => $item->price
                            ];
                        })
                    ];
                });

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
                    'total_users' => $totalUsers,
                    'total_products' => $totalProducts,
                    'total_payments' => $totalPayments,
                    'current_orders' => $currentOrders,
                    'total_revenue' => $totalRevenue,
                    'total_reviews' => $totalReviews,
                    'recent_sales' => $recentSales
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

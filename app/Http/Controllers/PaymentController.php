<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function process(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer',
                'success_url' => 'required|string'
            ]);

            // Get cart items with quantity
            $cartItems = DB::table('carts as pa')
                ->join('products as p', 'pa.product_id', '=', 'p.id')
                ->select('p.*', 'pa.user_id', 'pa.quantity')
                ->where('pa.user_id', $request->user_id)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'error' => 'Cart is empty'
                ], 400);
            }

            $total = 0;
            $lineItems = [];

            foreach($cartItems as $item) {
                $total += $item->price * $item->quantity;
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item->name,
                            'description' => $item->description,
                        ],
                        'unit_amount' => (int)($item->price * 100), // Convert to cents
                    ],
                    'quantity' => $item->quantity,
                ];
            }

            // Create Stripe Checkout Session
            $checkout_session = \Stripe\Checkout\Session::create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $request->success_url . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $request->success_url . '?canceled=true',
            ]);

            if ($checkout_session->url) {
                // Create order
                $commande_id = DB::table('orders')->insertGetId([
                    'user_id' => $request->user_id,
                    'total_amount' => $total,
                    'status' => 'pending',
                    'date_commande' => now(),
                    'stripe_session_id' => $checkout_session->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Create order items and update product quantities
                foreach($cartItems as $item) {
                    DB::table('ligne_commandes')->insert([
                        'commande_id' => $commande_id,
                        'product_id' => $item->id,
                        'quantite' => $item->quantity,
                        'sous_total' => $item->price * $item->quantity,
                        'etat' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                // Clear the cart
                DB::table('carts')->where('user_id', $request->user_id)->delete();

                return response()->json([
                    'url' => $checkout_session->url,
                    'session_id' => $checkout_session->id
                ]);
            }

            return response()->json([
                'error' => 'Failed to create checkout session'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function success(Request $request)
    {
        try {
            $session_id = $request->get('session_id');
            
            if (!$session_id) {
                return response()->json([
                    'error' => 'No session ID provided'
                ], 400);
            }

            // Retrieve the session from Stripe
            $session = \Stripe\Checkout\Session::retrieve($session_id);
            
            if ($session->payment_status === 'paid') {
                // Update order status
                DB::table('orders')
                    ->where('stripe_session_id', $session_id)
                    ->update([
                        'status' => 'paid',
                        'updated_at' => now()
                    ]);

                // Update order items status
                DB::table('ligne_commandes')
                    ->join('orders', 'ligne_commandes.commande_id', '=', 'orders.id')
                    ->where('orders.stripe_session_id', $session_id)
                    ->update([
                        'ligne_commandes.etat' => 'confirmed',
                        'ligne_commandes.updated_at' => now()
                    ]);

                // Update product quantities
                $orderItems = DB::table('ligne_commandes')
                    ->join('orders', 'ligne_commandes.commande_id', '=', 'orders.id')
                    ->where('orders.stripe_session_id', $session_id)
                    ->get();

                foreach ($orderItems as $item) {
                    DB::table('products')
                        ->where('id', $item->product_id)
                        ->decrement('quantite', $item->quantite);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment successful'
                ]);
            }

            return response()->json([
                'error' => 'Payment not completed'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

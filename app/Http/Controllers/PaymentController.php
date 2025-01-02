<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaymentController extends Controller
{
    public function process(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|integer',
                'success_url' => ['required', 'string', function($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('The success url must be a valid URL.');
                    }
                }]
            ]);

            $cartItems = DB::select('
                SELECT p.*, pa.user_id
                FROM carts pa
                JOIN products p ON pa.product_id = p.id
                WHERE pa.user_id = ?',
                [$request->user_id]
            );

            if (empty($cartItems)) {
                return response()->json([
                    'error' => 'Cart is empty'
                ], 400);
            }


            $total = 0;
            foreach($cartItems as $item) {
                $total += $item->price;
            }


            Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
            $checkout_session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'e-shop order'
                        ],
                        'unit_amount' => $total * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $request->success_url,
                'cancel_url' => $request->success_url . '?canceled=true',
            ]);

            if ($checkout_session->url) {

                $commande_id = DB::table('orders')->insertGetId([
                    'user_id' => $request->user_id,
                    'total_amount' => $total,
                    'status' => $request->status,
                    'date_commande' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);


                foreach($cartItems as $item) {
                    DB::table('ligne_commandes')->insert([
                        'commande_id' => $commande_id,
                        'product_id' => $item->id,
                        'quantite' => $quantite,
                        'sous_total' => $item->price,
                        'etat' => 'attente',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    DB::update('UPDATE products SET quantity = quantity - 1 WHERE id = ?', [$item->id]);
                }
                DB::delete('DELETE FROM cartes WHERE user_id = ?', [$request->user_id]);
            }

            return response()->json([
                'url' => $checkout_session->url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

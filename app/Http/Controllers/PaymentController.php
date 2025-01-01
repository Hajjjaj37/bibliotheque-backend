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
    private PayPalHttpClient $client;

    public function __construct()
    {
        // Disable SSL verification for development
        curl_setopt_array(curl_init(), [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $environment = new SandboxEnvironment(
            config('services.paypal.client_id'),
            config('services.paypal.client_secret')
        );
        
        $this->client = new PayPalHttpClient($environment);
    }

    public function createPayment(): JsonResponse
    {
        try {
            $cart = Cart::with(['items.product'])
                ->where('user_id', Auth::id())
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cart is empty'
                ], 400);
            }

            $total = $cart->items->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($total, 2, '.', '')
                    ]
                ]],
                'application_context' => [
                    'return_url' => route('payment.success'),
                    'cancel_url' => route('payment.cancel')
                ]
            ];

            $response = $this->client->execute($request);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'paypal_order_id' => $response->result->id,
                    'approval_url' => collect($response->result->links)
                        ->firstWhere('rel', 'approve')->href
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function capturePayment(Request $request): JsonResponse
    {
        try {
            $paypalOrderId = $request->input('paypal_order_id');
            $request = new OrdersCaptureRequest($paypalOrderId);
            $response = $this->client->execute($request);

            if ($response->result->status === 'COMPLETED') {
                // Create order from cart
                $cart = Cart::with(['items.product'])
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                $order = Order::create([
                    'user_id' => Auth::id(),
                    'total_amount' => $cart->items->sum(function ($item) {
                        return $item->quantity * $item->product->price;
                    }),
                    'status' => 'processing',
                    'payment_id' => $paypalOrderId,
                    'payment_status' => 'paid'
                ]);

                // Create order items and update stock
                foreach ($cart->items as $item) {
                    $order->items()->create([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->product->price
                    ]);

                    // Update product stock
                    $item->product->decrement('stock', $item->quantity);
                }

                // Clear the cart
                $cart->items()->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment completed successfully',
                    'data' => [
                        'order' => $order
                    ]
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Payment failed'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to capture payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function success(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Payment successful',
            'data' => $request->all()
        ], 200);
    }

    public function cancel(): JsonResponse
    {
        return response()->json([
            'status' => 'cancelled',
            'message' => 'Payment was cancelled'
        ], 200);
    }
} 
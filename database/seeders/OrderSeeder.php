<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Get all users and products
        $users = User::all();
        $products = Product::all();

        // Create 50 orders
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => 0, // Will be calculated based on items
                'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
                'payment_status' => fake()->randomElement(['pending', 'paid', 'failed'])
            ]);

            // Add 1-5 random products as order items
            $numItems = rand(1, 5);
            $total = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price
                ]);

                $total += $quantity * $price;
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Electronics
            [
                'name' => 'Smartphone X',
                'description' => 'Latest smartphone with advanced features',
                'price' => 999.99,
                'stock' => 50,
                'category_id' => 1,
                'image' => 'products/smartphone.jpg',
                'average_rating' => 4.5
            ],
            [
                'name' => 'Laptop Pro',
                'description' => 'High-performance laptop for professionals',
                'price' => 1499.99,
                'stock' => 30,
                'category_id' => 1,
                'image' => 'products/laptop.jpg',
                'average_rating' => 4.8
            ],
            // Clothing
            [
                'name' => 'Classic T-Shirt',
                'description' => 'Comfortable cotton t-shirt',
                'price' => 29.99,
                'stock' => 100,
                'category_id' => 2,
                'image' => 'products/tshirt.jpg',
                'average_rating' => 4.0
            ],
            [
                'name' => 'Denim Jeans',
                'description' => 'Classic blue denim jeans',
                'price' => 79.99,
                'stock' => 75,
                'category_id' => 2,
                'image' => 'products/jeans.jpg',
                'average_rating' => 4.2
            ],
            // Books
            [
                'name' => 'Programming Guide',
                'description' => 'Comprehensive programming guide for beginners',
                'price' => 49.99,
                'stock' => 60,
                'category_id' => 3,
                'image' => 'products/book1.jpg',
                'average_rating' => 4.6
            ],
            [
                'name' => 'Novel Collection',
                'description' => 'Collection of bestselling novels',
                'price' => 89.99,
                'stock' => 40,
                'category_id' => 3,
                'image' => 'products/book2.jpg',
                'average_rating' => 4.3
            ],
            // Home & Garden
            [
                'name' => 'Garden Tools Set',
                'description' => 'Complete set of essential garden tools',
                'price' => 149.99,
                'stock' => 25,
                'category_id' => 4,
                'image' => 'products/garden-tools.jpg',
                'average_rating' => 4.4
            ],
            [
                'name' => 'Decorative Lamp',
                'description' => 'Modern decorative lamp for home',
                'price' => 129.99,
                'stock' => 35,
                'category_id' => 4,
                'image' => 'products/lamp.jpg',
                'average_rating' => 4.1
            ],
            // Sports
            [
                'name' => 'Yoga Mat',
                'description' => 'Premium yoga mat with carrying strap',
                'price' => 39.99,
                'stock' => 80,
                'category_id' => 5,
                'image' => 'products/yoga-mat.jpg',
                'average_rating' => 4.7
            ],
            [
                'name' => 'Running Shoes',
                'description' => 'Professional running shoes',
                'price' => 119.99,
                'stock' => 45,
                'category_id' => 5,
                'image' => 'products/shoes.jpg',
                'average_rating' => 4.9
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 
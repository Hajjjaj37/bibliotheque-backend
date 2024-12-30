<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Books', 'description' => 'Books and publications'],
            ['name' => 'Home & Garden', 'description' => 'Home decor and garden supplies'],
            ['name' => 'Sports', 'description' => 'Sports equipment and accessories'],
            ['name' => 'Toys', 'description' => 'Toys and games for all ages'],
            ['name' => 'Beauty', 'description' => 'Beauty and personal care products'],
            ['name' => 'Food', 'description' => 'Food and beverages']
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 
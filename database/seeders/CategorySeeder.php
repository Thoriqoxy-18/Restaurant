<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Appetizer', 'slug' => 'appetizer', 'description' => 'Light starters to begin your meal'],
            ['name' => 'Main Course', 'slug' => 'main-course', 'description' => 'Signature main dishes from our chef'],
            ['name' => 'Dessert', 'slug' => 'dessert', 'description' => 'Sweet treats for a perfect ending'],
            ['name' => 'Drinks', 'slug' => 'drinks', 'description' => 'Refreshing beverages and cocktails'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

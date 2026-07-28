<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Appetizer (category_id: 1)
            ['category_id' => 1, 'name' => 'Truffle Wagyu Slider', 'slug' => 'truffle-wagyu-slider', 'price' => 85000, 'rating' => 4.8, 'is_best_seller' => true, 'display_order' => 1],
            ['category_id' => 1, 'name' => 'Crispy Calamari', 'slug' => 'crispy-calamari', 'price' => 48000, 'rating' => 4.5, 'is_best_seller' => true, 'display_order' => 2],
            ['category_id' => 1, 'name' => 'Spicy Tuna Roll', 'slug' => 'spicy-tuna-roll', 'price' => 52000, 'rating' => 4.7, 'display_order' => 3],
            ['category_id' => 1, 'name' => 'Heritage Burrata Salad', 'slug' => 'heritage-burrata-salad', 'price' => 65000, 'rating' => 4.7, 'is_signature' => true, 'display_order' => 4],
            ['category_id' => 1, 'name' => 'Caesar Fresh Salad', 'slug' => 'caesar-fresh-salad', 'price' => 55000, 'rating' => 4.3, 'display_order' => 5],
            ['category_id' => 1, 'name' => 'Creamy Mushroom Soup', 'slug' => 'creamy-mushroom-soup', 'price' => 42000, 'rating' => 4.5, 'display_order' => 6],

            // Main Course (category_id: 2)
            ['category_id' => 2, 'name' => 'Smoked Salmon Bowl', 'slug' => 'smoked-salmon-bowl', 'price' => 92000, 'rating' => 4.9, 'is_signature' => true, 'display_order' => 1],
            ['category_id' => 2, 'name' => 'Beef Rendang Nasi', 'slug' => 'beef-rendang-nasi', 'price' => 65000, 'rating' => 4.8, 'is_signature' => true, 'display_order' => 2],
            ['category_id' => 2, 'name' => 'Grilled Chicken Wrap', 'slug' => 'grilled-chicken-wrap', 'price' => 58000, 'rating' => 4.4, 'is_best_seller' => true, 'display_order' => 3],
            ['category_id' => 2, 'name' => 'Creamy Mushroom Pasta', 'slug' => 'creamy-mushroom-pasta', 'price' => 72000, 'rating' => 4.5, 'display_order' => 4],
            ['category_id' => 2, 'name' => 'Prawn Linguine', 'slug' => 'prawn-linguine', 'price' => 88000, 'rating' => 4.7, 'display_order' => 5],
            ['category_id' => 2, 'name' => 'Lamb Kofta Kebab', 'slug' => 'lamb-kofta-kebab', 'price' => 68000, 'rating' => 4.6, 'is_best_seller' => true, 'display_order' => 6],

            // Dessert (category_id: 3)
            ['category_id' => 3, 'name' => 'Velvet Lava Cake', 'slug' => 'velvet-lava-cake', 'price' => 45000, 'rating' => 4.8, 'is_signature' => true, 'display_order' => 1],
            ['category_id' => 3, 'name' => 'Mango Sticky Rice', 'slug' => 'mango-sticky-rice', 'price' => 38000, 'rating' => 4.9, 'is_best_seller' => true, 'display_order' => 2],
            ['category_id' => 3, 'name' => 'Crème Brûlée', 'slug' => 'creme-brulee', 'price' => 42000, 'rating' => 4.7, 'display_order' => 3],

            // Drinks (category_id: 4)
            ['category_id' => 4, 'name' => 'Botanical Cucumber Gin', 'slug' => 'botanical-cucumber-gin', 'price' => 55000, 'rating' => 4.9, 'is_best_seller' => true, 'display_order' => 1],
            ['category_id' => 4, 'name' => 'Classic Mojito', 'slug' => 'classic-mojito', 'price' => 48000, 'rating' => 4.5, 'display_order' => 2],
            ['category_id' => 4, 'name' => 'Espresso Martini', 'slug' => 'espresso-martini', 'price' => 62000, 'rating' => 4.7, 'is_best_seller' => true, 'display_order' => 3],
            ['category_id' => 4, 'name' => 'Espresso', 'slug' => 'espresso', 'price' => 25000, 'rating' => 4.6, 'display_order' => 4],
            ['category_id' => 4, 'name' => 'Cappuccino', 'slug' => 'cappuccino', 'price' => 32000, 'rating' => 4.5, 'display_order' => 5],
            ['category_id' => 4, 'name' => 'Matcha Latte', 'slug' => 'matcha-latte', 'price' => 38000, 'rating' => 4.8, 'display_order' => 6],
            ['category_id' => 4, 'name' => 'Berry Smoothie Bowl', 'slug' => 'berry-smoothie-bowl', 'price' => 42000, 'rating' => 4.6, 'display_order' => 7],
            ['category_id' => 4, 'name' => 'Fresh Lemonade Mint', 'slug' => 'fresh-lemonade-mint', 'price' => 32000, 'rating' => 4.3, 'display_order' => 8],
            ['category_id' => 4, 'name' => 'Lychee Splash', 'slug' => 'lychee-splash', 'price' => 38000, 'rating' => 4.4, 'display_order' => 9],
        ];

        $counter = 1;
        foreach ($items as $item) {
            $item['description'] = 'Nikmati ' . $item['name'] . ' pilihan terbaik dari Verdant Bistro. Dibuat dengan bahan-bahan segar dan berkualitas premium.';
            $item['image'] = 'assets/images/menu/menu-' . $counter . '.svg';
            $item['badge'] = null;
            $counter++;
            MenuItem::create($item);
        }
    }
}

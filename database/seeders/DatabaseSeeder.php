<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuItemOption;
use App\Models\MenuItemOptionChoice;
use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create(['name' => 'Owner Restoran', 'email' => 'owner@resto.com', 'password' => bcrypt('password'), 'role' => 'owner', 'is_active' => true]);
        User::create(['name' => 'Kasir Utama', 'email' => 'kasir@resto.com', 'password' => bcrypt('password'), 'role' => 'kasir', 'is_active' => true]);

        $cats = ['Makanan Utama', 'Minuman', 'Dessert', 'Cemilan'];
        foreach ($cats as $i => $name) {
            Category::create(['name' => $name, 'slug' => Str::slug($name), 'sort_order' => $i]);
        }

        $catIds = Category::pluck('id', 'slug');

        $menuDefs = [
            // Snacks / Appetizers
            ['name' => 'Truffle Wagyu Slider', 'price' => 85000, 'is_bestseller' => true, 'rating' => 4.8, 'cat' => 'cemilan'],
            ['name' => 'Crispy Calamari', 'price' => 48000, 'rating' => 4.5, 'cat' => 'cemilan'],
            ['name' => 'Spicy Tuna Roll', 'price' => 52000, 'rating' => 4.7, 'cat' => 'cemilan', 'has_spice' => true],
            // Main Course
            ['name' => 'Smoked Salmon Bowl', 'price' => 92000, 'is_signature' => true, 'rating' => 4.9, 'cat' => 'makanan-utama'],
            ['name' => 'Beef Rendang Nasi', 'price' => 65000, 'is_signature' => true, 'rating' => 4.8, 'cat' => 'makanan-utama'],
            ['name' => 'Grilled Chicken Wrap', 'price' => 58000, 'is_bestseller' => true, 'rating' => 4.4, 'cat' => 'makanan-utama'],
            ['name' => 'Prawn Linguine', 'price' => 88000, 'rating' => 4.7, 'cat' => 'makanan-utama'],
            ['name' => 'Lamb Kofta Kebab', 'price' => 68000, 'rating' => 4.6, 'cat' => 'makanan-utama'],
            // Drinks
            ['name' => 'Botanical Cucumber Gin', 'price' => 55000, 'rating' => 4.9, 'cat' => 'minuman'],
            ['name' => 'Espresso Martini', 'price' => 62000, 'is_bestseller' => true, 'rating' => 4.7, 'cat' => 'minuman'],
            ['name' => 'Matcha Latte', 'price' => 38000, 'rating' => 4.8, 'cat' => 'minuman'],
            ['name' => 'Berry Smoothie', 'price' => 42000, 'rating' => 4.6, 'cat' => 'minuman'],
            ['name' => 'Classic Mojito', 'price' => 48000, 'rating' => 4.5, 'cat' => 'minuman'],
            // Dessert
            ['name' => 'Velvet Lava Cake', 'price' => 45000, 'is_signature' => true, 'rating' => 4.8, 'cat' => 'dessert'],
            ['name' => 'Mango Sticky Rice', 'price' => 38000, 'is_bestseller' => true, 'rating' => 4.9, 'cat' => 'dessert'],
            ['name' => 'Crème Brûlée', 'price' => 42000, 'rating' => 4.7, 'cat' => 'dessert'],
            // Snacks
            ['name' => 'French Fries', 'price' => 28000, 'rating' => 4.3, 'cat' => 'cemilan'],
            ['name' => 'Chicken Wings', 'price' => 35000, 'is_bestseller' => true, 'rating' => 4.6, 'cat' => 'cemilan'],
        ];

        $items = [];
        foreach ($menuDefs as $i => $def) {
            $item = MenuItem::create([
                'category_id' => $catIds[$def['cat']],
                'name' => $def['name'],
                'slug' => Str::slug($def['name']),
                'description' => 'Nikmati ' . $def['name'] . ' pilihan terbaik dari restoran kami.',
                'price' => $def['price'],
                'has_spice_level' => $def['has_spice'] ?? false,
                'is_signature' => $def['is_signature'] ?? false,
                'is_bestseller' => $def['is_bestseller'] ?? false,
                'rating' => $def['rating'] ?? null,
                'image_path' => 'assets/images/menu/menu-' . ($i + 1) . '.svg',
            ]);
            $items[] = $item;
        }

        // Dynamic options per menu category

        // Main Course (item 4-8): doneness + extra sides
        foreach ([3,4,5,6,7] as $i) {
            $opt = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Tingkat Kematangan', 'type' => 'radio', 'sort_order' => 1]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt->id, 'name' => 'Medium Rare', 'price_modifier' => 0, 'sort_order' => 1],
                ['menu_item_option_id' => $opt->id, 'name' => 'Medium', 'price_modifier' => 0, 'sort_order' => 2],
                ['menu_item_option_id' => $opt->id, 'name' => 'Well Done', 'price_modifier' => 0, 'sort_order' => 3],
            ]);
            $opt2 = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Tambahan Lauk', 'type' => 'checkbox', 'sort_order' => 2]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt2->id, 'name' => 'Telur Mata Sapi', 'price_modifier' => 5000, 'sort_order' => 1],
                ['menu_item_option_id' => $opt2->id, 'name' => 'Kentang Goreng', 'price_modifier' => 8000, 'sort_order' => 2],
                ['menu_item_option_id' => $opt2->id, 'name' => 'Tumis Sayur', 'price_modifier' => 6000, 'sort_order' => 3],
            ]);
        }

        // Drinks (item 9-13): size + sugar level
        foreach ([8,9,10,11,12] as $i) {
            $opt = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Pilih Ukuran', 'type' => 'radio', 'sort_order' => 1]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt->id, 'name' => 'Kecil', 'price_modifier' => 0, 'sort_order' => 1],
                ['menu_item_option_id' => $opt->id, 'name' => 'Sedang', 'price_modifier' => 5000, 'sort_order' => 2],
                ['menu_item_option_id' => $opt->id, 'name' => 'Besar', 'price_modifier' => 10000, 'sort_order' => 3],
            ]);
            $opt2 = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Tingkat Gula', 'type' => 'radio', 'sort_order' => 2]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt2->id, 'name' => 'Less Sugar', 'price_modifier' => 0, 'sort_order' => 1],
                ['menu_item_option_id' => $opt2->id, 'name' => 'Normal', 'price_modifier' => 0, 'sort_order' => 2],
                ['menu_item_option_id' => $opt2->id, 'name' => 'Extra Sweet', 'price_modifier' => 2000, 'sort_order' => 3],
            ]);
            $opt3 = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Topping Minuman', 'type' => 'checkbox', 'sort_order' => 3]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt3->id, 'name' => 'Boba', 'price_modifier' => 5000, 'sort_order' => 1],
                ['menu_item_option_id' => $opt3->id, 'name' => 'Grass Jelly', 'price_modifier' => 4000, 'sort_order' => 2],
                ['menu_item_option_id' => $opt3->id, 'name' => 'Ice Cream', 'price_modifier' => 7000, 'sort_order' => 3],
            ]);
        }

        // Dessert (item 14-16): extra topping
        foreach ([13,14,15] as $i) {
            $opt = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Topping Dessert', 'type' => 'checkbox', 'sort_order' => 1]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt->id, 'name' => 'Coklat Cair', 'price_modifier' => 5000, 'sort_order' => 1],
                ['menu_item_option_id' => $opt->id, 'name' => 'Strawberry', 'price_modifier' => 4000, 'sort_order' => 2],
                ['menu_item_option_id' => $opt->id, 'name' => 'Kacang Almond', 'price_modifier' => 6000, 'sort_order' => 3],
            ]);
        }

        // Cemilan (items 1-3, 17-18): variant + sauce
        foreach ([0,1,2,16,17] as $i) {
            $opt = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Pilih Porsi', 'type' => 'radio', 'sort_order' => 1]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt->id, 'name' => 'Reguler', 'price_modifier' => 0, 'sort_order' => 1],
                ['menu_item_option_id' => $opt->id, 'name' => 'Large', 'price_modifier' => 10000, 'sort_order' => 2],
            ]);
            $opt2 = MenuItemOption::create(['menu_item_id' => $items[$i]->id, 'group_name' => 'Saus', 'type' => 'checkbox', 'sort_order' => 2]);
            MenuItemOptionChoice::insert([
                ['menu_item_option_id' => $opt2->id, 'name' => 'Sambal', 'price_modifier' => 0, 'sort_order' => 1],
                ['menu_item_option_id' => $opt2->id, 'name' => 'Mayonaise', 'price_modifier' => 0, 'sort_order' => 2],
                ['menu_item_option_id' => $opt2->id, 'name' => 'Keju', 'price_modifier' => 4000, 'sort_order' => 3],
            ]);
        }

        // Tables
        foreach (['A1','A2','A3','B1','B2','C1'] as $code) {
            RestaurantTable::create([
                'code' => $code,
                'capacity' => in_array($code, ['A1','A2']) ? 2 : 4,
                'qr_token' => Str::random(64),
            ]);
        }
    }
}

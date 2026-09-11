<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\MenuItemSauce;
use App\Models\MenuItemTopping;
use App\Models\MenuItemVariation;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * PERHATIAN (sebelum go-live / dipakai user asli):
 * Di environment NON-produksi, akun seeder memakai password default 'password'
 * (admin@verdant.test & kasir@verdant.test). WAJIB ganti semua password default
 * sebelum aplikasi dipakai oleh user asli — baik lewat menu Admin > Pengguna,
 * lewat php artisan tinker, atau reset password saat provisioning.
 * Di environment production, seeder otomatis membuat password acak (lihat di bawah).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== User default (diperbaiki: role & is_active diset eksplisit, bukan mass-assign) =====
        // Role TIDAK boleh lewat fillable (app/Models/User.php), jadi diset manual setelah create.
        $isProduction = app()->environment('production');
        $adminPassword = $isProduction ? Str::random(16) : 'password';
        $kasirPassword = $isProduction ? Str::random(16) : 'password';

        $admin = User::firstOrCreate(
            ['email' => 'admin@verdant.test'],
            ['name' => 'Admin', 'password' => $adminPassword]
        );
        $admin->role = 'admin';
        $admin->is_active = true;
        $admin->save();

        $kasir = User::firstOrCreate(
            ['email' => 'kasir@verdant.test'],
            ['name' => 'Kasir', 'password' => $kasirPassword]
        );
        $kasir->role = 'kasir';
        $kasir->is_active = true;
        $kasir->save();

        if ($isProduction && $this->command) {
            $this->command->info('Akun Admin : admin@verdant.test / '.$adminPassword);
            $this->command->info('Akun Kasir : kasir@verdant.test / '.$kasirPassword);
        }

        $cats = [
            ['name' => 'Appetizer', 'slug' => 'appetizer', 'sort_order' => 1],
            ['name' => 'Main Course', 'slug' => 'main-course', 'sort_order' => 2],
            ['name' => 'Dessert', 'slug' => 'dessert', 'sort_order' => 3],
            ['name' => 'Drinks', 'slug' => 'drinks', 'sort_order' => 4],
            ['name' => 'Snacks', 'slug' => 'snacks', 'sort_order' => 5],
        ];
        foreach ($cats as $c) Category::updateOrCreate(['slug' => $c['slug']], $c);
        $catMap = Category::pluck('id', 'slug');

        $m = function($data, $vars, $toppings, $sauces) use ($catMap) {
            $slug = Str::slug($data['name']);
            $item = MenuItem::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $catMap[$data['cat']],
                    'name' => $data['name'],
                    'description' => $data['desc'],
                    'price' => $data['price'],
                    'old_price' => $data['old_price'] ?? null,
                    'is_bestseller' => $data['bestseller'] ?? false,
                    'is_signature' => $data['signature'] ?? false,
                    'rating' => $data['rating'] ?? null,
                    'prep_time_minutes' => $data['prep'],
                    'is_vegan' => $data['vegan'] ?? false,
                    'has_spice_level' => $data['spice'] ?? false,
                ]
            );
            $jpeg = 'assets/images/menu/' . $slug . '.jpeg';
            $svg = 'assets/images/menu/' . $slug . '.svg';
            $item->image_path = file_exists(public_path($jpeg)) ? $jpeg : $svg;
            $item->save();
            $item->variations()->delete();
            $item->toppings()->delete();
            $item->sauces()->delete();
            foreach ($vars as $v) MenuItemVariation::create(['menu_item_id' => $item->id, 'name' => $v[0], 'extra_price' => $v[1]]);
            foreach ($toppings as $t) MenuItemTopping::create(['menu_item_id' => $item->id, 'name' => $t[0], 'extra_price' => $t[1]]);
            foreach ($sauces as $s) MenuItemSauce::create(['menu_item_id' => $item->id, 'name' => $s[0], 'extra_price' => $s[1]]);
        };

        // =============================================
        // APPETIZER
        // =============================================

        // 1. Risoles Mayo — Regular (base), Large (+12k). Sauce options.
        $m(
            ['name' => 'Risoles Mayo', 'cat' => 'appetizer', 'price' => 25000, 'desc' => 'Risoles renyah dengan isian smoked beef, telur dan mayonaise.', 'prep' => 8, 'signature' => true, 'rating' => 4.7],
            [['Large', 12000]],
            [],
            [['Chili', 0], ['Mayonnaise', 0], ['Thousand Island', 0]]
        );

        // 2. Crispy Spring Roll — Original (base), Cheese (+4k), Spicy (+2k).
        $m(
            ['name' => 'Crispy Spring Roll', 'cat' => 'appetizer', 'price' => 22000, 'desc' => 'Lumpia renyah berisi sayuran segar.', 'prep' => 10, 'signature' => true, 'rating' => 4.7],
            [['Cheese Spring Roll', 4000], ['Spicy Spring Roll', 2000]],
            [['Extra Sauce', 3000]],
            [['Sweet Chili', 0], ['Garlic Mayo', 0]]
        );

        // 3. Chicken Dumpling — Steamed (base), Fried (+2k).
        $m(
            ['name' => 'Chicken Dumpling', 'cat' => 'appetizer', 'price' => 32000, 'desc' => 'Dumpling ayam dengan kulit lembut dan isian juicy. Pilih steam atau fried.', 'prep' => 12, 'signature' => true, 'rating' => 4.9],
            [['Fried Dumpling', 2000]],
            [],
            [['Chili Oil', 0], ['Soy Sauce', 0], ['Garlic Sauce', 0]]
        );

        // =============================================
        // SNACKS
        // =============================================

        // 4. Cireng Crispy — Original (base), Jumbo (+8k), Mozzarella (+10k).
        $m(
            ['name' => 'Cireng Crispy', 'cat' => 'snacks', 'price' => 22000, 'desc' => 'Cireng renyah disajikan dengan bumbu rujak khas.', 'prep' => 8, 'bestseller' => true, 'spice' => true, 'rating' => 4.8],
            [['Jumbo', 8000], ['Mozzarella Cireng', 10000]],
            [['Extra Bumbu Rujak', 3000]],
            []
        );

        // 5. Crinkle Fries — Original (base), BBQ (+3k), Cheese (+5k), Balado (+3k).
        $m(
            ['name' => 'Crinkle Fries', 'cat' => 'snacks', 'price' => 28000, 'desc' => 'Kentang goreng crinkle yang renyah di luar dan lembut di dalam.', 'prep' => 8, 'bestseller' => true, 'rating' => 4.8],
            [['BBQ Crinkle', 3000], ['Cheese Crinkle', 5000], ['Balado Crinkle', 3000]],
            [],
            [['Tomato', 0], ['Chili', 0], ['Mayonnaise', 0]]
        );

        // 6. Onion Rings — Original (base), BBQ (+3k), Cheese (+5k).
        $m(
            ['name' => 'Onion Rings', 'cat' => 'snacks', 'price' => 25000, 'desc' => 'Cincin bawang renyah dengan lapisan tepung crispy.', 'prep' => 8, 'rating' => 4.5],
            [['BBQ Onion Rings', 3000], ['Cheese Onion Rings', 5000]],
            [],
            [['BBQ', 0], ['Cheese', 3000], ['Thousand Island', 0]]
        );

        // =============================================
        // MAIN COURSE
        // =============================================

        // 7. Chicken Katsu Rice — Original (base), Spicy (+3k). Toppings + sauces.
        $m(
            ['name' => 'Chicken Katsu Rice', 'cat' => 'main-course', 'price' => 42000, 'desc' => 'Chicken katsu crispy disajikan dengan nasi hangat.', 'prep' => 15, 'bestseller' => true, 'rating' => 4.6],
            [['Spicy Katsu', 3000], ['Large + Nasi', 10000]],
            [['Extra Chicken', 15000], ['Egg', 6000]],
            [['BBQ', 0], ['Black Pepper', 0], ['Teriyaki', 0]]
        );

        // 8. Ayam Geprek — Nasi (base), Nasi + Telur (+7k). Toppings. Level pedas built-in.
        $m(
            ['name' => 'Ayam Geprek', 'cat' => 'main-course', 'price' => 35000, 'desc' => 'Ayam crispy geprek dengan sambal pilihan.', 'prep' => 15, 'signature' => true, 'spice' => true, 'rating' => 4.9],
            [['Nasi + Telur', 7000]],
            [['Extra Sambal', 2000],['Telur', 6000], ['Keju Mozzarella', 8000]],
            []
        );

        // 9. Salmon Poke Bowl — Regular (base), Large (+12k). Toppings + sauces.
        $m(
            ['name' => 'Salmon Poke Bowl', 'cat' => 'main-course', 'price' => 48000, 'desc' => 'Rice bowl sehat dengan salmon segar dan sayuran.', 'prep' => 15, 'signature' => true, 'rating' => 4.8],
            [['Large', 12000]],
            [['Extra Salmon', 18000], ['Avocado', 8000], ['Edamame', 5000]],
            [['Soy Sesame', 0], ['Spicy Mayo', 0]]
        );

        // 10. Salmon Ramen — Regular (base), Large (+10k). Toppings. Level pedas built-in.
        $m(
            ['name' => 'Salmon Ramen', 'cat' => 'main-course', 'price' => 55000, 'desc' => 'Ramen kuah gurih dengan potongan salmon.', 'prep' => 18, 'bestseller' => true, 'spice' => true, 'rating' => 4.9],
            [['Large', 10000]],
            [['Extra Egg', 6000], ['Extra Noodle', 8000]],
            []
        );

        // 11. Avocado Salad Bowl — Regular (base), Large (+10k). Toppings + dressings.
        $m(
            ['name' => 'Avocado Salad Bowl', 'cat' => 'main-course', 'price' => 34000, 'desc' => 'Perpaduan alpukat segar, ayam panggang, telur, edamame, jagung manis, tomat ceri.', 'prep' => 12, 'signature' => true, 'rating' => 4.9],
            [['Large', 10000]],
            [['Extra Avocado', 8000], ['Extra Chicken', 15000], ['Extra Egg', 6000], ['Extra Edamame', 5000]],
            [['Sesame Dressing', 0], ['Japanese Mayo', 0], ['Spicy Mayo', 0], ['Lemon Vinaigrette', 0]]
        );

        // =============================================
        // DESSERT
        // =============================================

        // 12. Banana Nugget — Regular (base), Large (+8k). Toppings as flavor options.
        $m(
            ['name' => 'Banana Nugget', 'cat' => 'dessert', 'price' => 28000, 'old_price' => 35000, 'desc' => 'Pisang nugget crispy dengan topping premium.', 'prep' => 10, 'rating' => 4.5],
            [['Large', 8000]],
            [['Chocolate', 2000], ['Cheese', 2000], ['Matcha', 3000], ['Tiramisu', 3000]],
            []
        );

        // 13. Waffle Ice Cream — Ice cream flavor variations. Topping sauces.
        $m(
            ['name' => 'Waffle Ice Cream', 'cat' => 'dessert', 'price' => 32000, 'desc' => 'Waffle hangat dengan es krim vanilla pilihan.', 'prep' => 8, 'rating' => 4.7],
            [['Chocolate Ice Cream', 3000], ['Strawberry Ice Cream', 3000]],
            [['Chocolate Sauce', 3000], ['Caramel', 3000], ['Oreo Crumbs', 4000]],
            []
        );

        // 14. Raspberry Cake — Slice (base), Whole Cake (+120k). Toppings.
        $m(
            ['name' => 'Raspberry Cake', 'cat' => 'dessert', 'price' => 35000, 'old_price' => 42000, 'desc' => 'Cake lembut dengan topping raspberry.', 'prep' => 5, 'rating' => 4.6],
            [['Whole Cake', 120000]],
            [['Extra Raspberry', 6000], ['Vanilla Ice Cream', 10000]],
            []
        );

        // =============================================
        // DRINKS
        // =============================================

        // 15. Iced Mocha — Regular (base), Large (+5k). Toppings handle sugar/ice levels.
        $m(
            ['name' => 'Iced Mocha', 'cat' => 'drinks', 'price' => 28000, 'desc' => 'Minuman kopi mocha dingin.', 'prep' => 5, 'rating' => 4.6],
            [['Large', 5000]],
            [['Less Sugar', 0], ['No Sugar', 0], ['Less Ice', 0], ['No Ice', 0]],
            []
        );

        // 16. Caramel Macchiato — Regular (base), Large (+5k). Milk options. Hot/Ice.
        $m(
            ['name' => 'Caramel Macchiato', 'cat' => 'drinks', 'price' => 35000, 'desc' => 'Espresso dengan susu dan caramel.', 'prep' => 5, 'signature' => true, 'rating' => 4.8],
            [['Large', 5000]],
            [['Oat Milk', 6000], ['Less Sugar', 0]],
            []
        );

        // 17. Avocado Juice (Drinks) — Signature
        $m(
            ['name' => 'Avocado Juice', 'cat' => 'drinks', 'price' => 22000, 'desc' => 'Fresh avocado blended with milk and ice, creating a rich, creamy, and refreshing drink.', 'prep' => 7, 'signature' => true, 'rating' => 4.9],
            [['Large', 6000]],
            [['Extra Avocado', 6000], ['Extra Milk', 3000], ['Chocolate Syrup', 3000], ['Less Sugar', 0], ['No Sugar', 0]],
            []
        );

        // Tables
        $this->call(RestaurantTableSeeder::class);
    }
}

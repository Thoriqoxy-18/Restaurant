<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminController extends Controller
{
    // ==================== DASHBOARD ====================

    public function home(): View
    {
        $stats = [
            'orders' => Order::whereDate('created_at', today())->count(),
            'revenue' => Order::where('payment_status', 'paid')->whereDate('created_at', today())->sum('total'),
            'processing' => Order::whereIn('status', ['confirmed', 'preparing'])->count(),
            'sold' => DB::table('order_items')->whereDate('created_at', today())->sum('quantity'),
        ];

        $last7 = collect(range(6, 0))->map(function ($i) {
            $date = now()->subDays($i)->startOfDay();
            $amount = Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$date, $date->copy()->endOfDay()])
                ->sum('total');

            return [
                'label' => $date->format('D'),
                'amount' => (float) $amount,
            ];
        });
        $max = max(1, $last7->max('amount'));

        $recentOrders = Order::with(['restaurantTable'])
            ->latest()
            ->take(6)
            ->get();

        $topMenus = DB::table('order_items')
            ->join('menu_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->select('menu_items.name', 'menu_items.price', 'menu_items.image_path', DB::raw('SUM(order_items.quantity) as total'))
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.price', 'menu_items.image_path')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        return view('admin.admin.dashboard', compact('stats', 'last7', 'max', 'recentOrders', 'topMenus'));
    }

    // ==================== MANAJEMEN MENU ====================

    public function menuIndex(): View
    {
        $menus = MenuItem::with('category')->orderBy('name')->get();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menu.index', compact('menus', 'categories'));
    }

    public function menuCreate(): View
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menu.form', ['categories' => $categories, 'menu' => null]);
    }

    public function menuStore(Request $request): RedirectResponse
    {
        $data = $this->menuData($request);
        $menu = MenuItem::create($data);
        $this->saveOptions($menu, $request);

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function menuEdit(MenuItem $menu): View
    {
        $menu->load(['variations', 'toppings', 'sauces']);
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.menu.form', compact('menu', 'categories'));
    }

    public function menuUpdate(Request $request, MenuItem $menu): RedirectResponse
    {
        $data = $this->menuData($request, $menu);
        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request);
        }
        $menu->update($data);
        $this->saveOptions($menu, $request);

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil diperbarui.');
    }

    public function menuDestroy(MenuItem $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()->route('admin.menu')->with('success', 'Menu dihapus.');
    }

    protected function menuData(Request $request, ?MenuItem $menu = null): array
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'old_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:1'],
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $menu ? $menu->slug : Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'old_price' => $request->old_price ?: null,
            'prep_time_minutes' => $request->prep_time_minutes ?: 15,
            'is_vegan' => $request->boolean('is_vegan'),
            'has_spice_level' => $request->boolean('has_spice_level'),
            'is_signature' => $request->boolean('is_signature'),
            'is_bestseller' => $request->boolean('is_bestseller'),
            'is_available' => $request->boolean('is_available'),
        ];

        if ($request->hasFile('image')) {
            $data['image_path'] = $this->storeImage($request);
        }

        return $data;
    }

    protected function storeImage(Request $request): string
    {
        $file = $request->file('image');
        $dir = public_path('assets/images/menu');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'jpeg';
        $filename = Str::slug($request->name).'-'.time().'.'.$ext;
        $file->move($dir, $filename);

        return 'assets/images/menu/'.$filename;
    }

    protected function saveOptions(MenuItem $menu, Request $request): void
    {
        foreach (['variations' => $menu->variations(), 'toppings' => $menu->toppings(), 'sauces' => $menu->sauces()] as $field => $relation) {
            $relation->delete();
            $rows = $request->input($field, []);
            $i = 0;
            foreach ($rows as $row) {
                if (isset($row['name']) && trim((string) $row['name']) !== '') {
                    $relation->create([
                        'menu_item_id' => $menu->id,
                        'name' => trim((string) $row['name']),
                        'extra_price' => (float) ($row['extra_price'] ?? 0),
                        'sort_order' => $i,
                    ]);
                }
                $i++;
            }
        }
    }

    // ==================== MANAJEMEN PENGGUNA ====================

    public function userIndex(): View
    {
        $users = User::orderBy('role')->orderBy('name')->get();

        return view('admin.users.index', compact('users'));
    }

    public function userStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:kasir,admin'],
        ]);

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function userUpdate(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:kasir,admin'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->is_active = $request->boolean('is_active');
        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }
        $user->save();

        return redirect()->route('admin.users')->with('success', 'Pengguna diperbarui.');
    }

    public function userToggle(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri.');
        }
        $user->is_active = ! $user->is_active;
        $user->save();

        return back()->with('success', 'Status pengguna diperbarui.');
    }

    public function userDestroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }

    // ==================== MEJA & QR ====================

    public function tableQr(): View
    {
        $tables = RestaurantTable::orderBy('table_number')->get();
        $stats = [
            'all' => $tables->count(),
            'available' => $tables->where('status', 'available')->count(),
            'occupied' => $tables->where('status', 'occupied')->count(),
        ];

        return view('admin.tables-qr.index', compact('tables', 'stats'));
    }

    public function tableStore(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        $num = RestaurantTable::max('table_number') + 1;
        $code = 'A'.str_pad((string) $num, 2, '0', STR_PAD_LEFT);

        RestaurantTable::create([
            'code' => $code,
            'qr_token' => 'table-'.strtolower($code),
            'table_number' => $num,
            'name' => $request->name,
            'capacity' => (int) $request->capacity,
            'status' => 'available',
        ]);

        return redirect()->route('admin.tables')->with('success', 'Meja berhasil ditambahkan.');
    }

    public function tableDestroy(RestaurantTable $table): RedirectResponse
    {
        $table->delete();

        return redirect()->route('admin.tables')->with('success', 'Meja dihapus.');
    }

    public function tableQrDownload(RestaurantTable $table)
    {
        $png = \App\Support\DemoQrCode::png(url('/menu?table='.$table->id), 14, 4);
        $name = 'qr-meja-'.Str::slug($table->label).'.png';
        $headers = ['Content-Type' => 'image/png'];

        if (request()->has('inline')) {
            return response($png, 200, $headers);
        }

        $headers['Content-Disposition'] = 'attachment; filename="'.$name.'"';

        return response($png, 200, $headers);
    }
}

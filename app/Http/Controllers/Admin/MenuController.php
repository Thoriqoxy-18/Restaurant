<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::with('category');

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $menus = $query->orderBy('category_id')->orderBy('name')->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.menus.form', ['menu' => new MenuItem, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'badge' => 'nullable|string|max:50',
            'is_available' => 'boolean',
            'is_popular' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_popular'] = $request->boolean('is_popular', false);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/menu', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        MenuItem::create($validated);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(MenuItem $menu)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.menus.form', ['menu' => $menu->load('category'), 'categories' => $categories]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'rating' => 'nullable|numeric|min:0|max:5',
            'badge' => 'nullable|string|max:50',
            'is_available' => 'boolean',
            'is_popular' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_popular'] = $request->boolean('is_popular', false);

        if ($request->hasFile('image')) {
            if ($menu->image && str_starts_with($menu->image, 'storage/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $menu->image));
            }
            $path = $request->file('image')->store('images/menu', 'public');
            $validated['image'] = 'storage/' . $path;
        }

        $menu->update($validated);

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(MenuItem $menu)
    {
        if ($menu->orderItems()->count() > 0) {
            return redirect()->route('admin.menus.index')
                ->with('error', 'Menu tidak bisa dihapus karena sudah ada di pesanan.');
        }

        if ($menu->image && str_starts_with($menu->image, 'storage/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $menu->image));
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}

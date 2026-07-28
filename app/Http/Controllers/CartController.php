<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(): array
    {
        return session()->get('cart', []);
    }

    private function saveCart(array $cart): void
    {
        session()->put('cart', array_values($cart));
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'integer|min:1',
            'spice_level' => 'nullable|integer|min:1|max:8',
            'notes' => 'nullable|string|max:255',
        ]);

        $menu = MenuItem::findOrFail($data['menu_item_id']);
        $cart = $this->getCart();
        $key = $data['menu_item_id'] . '_' . ($data['spice_level'] ?? 0);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $data['quantity'] ?? 1;
        } else {
            $cart[$key] = [
                'id' => $menu->id, 'name' => $menu->name, 'price' => (float) $menu->price,
                'quantity' => $data['quantity'] ?? 1,
                'spice_level' => $data['spice_level'] ?? null,
                'notes' => $data['notes'] ?? '',
                'image' => $menu->image_path ? asset($menu->image_path) : null,
            ];
        }

        $this->saveCart($cart);

        return response()->json([
            'success' => true,
            'count' => array_sum(array_column($cart, 'quantity')),
            'cart' => array_values($cart),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1',
            'spice_level' => 'nullable|integer|min:1|max:8',
        ]);

        $cart = $this->getCart();
        $key = $data['menu_item_id'] . '_' . ($data['spice_level'] ?? 0);

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = $data['quantity'];
        }

        $this->saveCart($cart);
        return response()->json(['success' => true, 'cart' => array_values($cart)]);
    }

    public function remove(Request $request): JsonResponse
    {
        $data = $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'spice_level' => 'nullable|integer|min:1|max:8',
        ]);

        $cart = $this->getCart();
        $key = $data['menu_item_id'] . '_' . ($data['spice_level'] ?? 0);
        unset($cart[$key]);

        $this->saveCart($cart);
        return response()->json(['success' => true, 'cart' => array_values($cart)]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;

class MenuController extends Controller
{
    public function show($id)
    {
        $menuItem = MenuItem::with('category')->findOrFail($id);
        $cart = session()->get('cart', []);

        return view('customer.pages.menu', compact('menuItem', 'cart'));
    }
}

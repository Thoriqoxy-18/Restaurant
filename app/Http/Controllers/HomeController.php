<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;

class HomeController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::with(['category', 'options.choices'])
            ->where('is_available', true)
            ->orderBy('is_signature', 'desc')
            ->orderBy('is_bestseller', 'desc')
            ->get();

        $categories = Category::orderBy('sort_order')->get();
        $tableNumber = session('table_number');

        return view('customer.home', compact('menuItems', 'categories', 'tableNumber'));
    }
}

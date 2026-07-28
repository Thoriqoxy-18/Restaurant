<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    public function index(Request $request)
    {
        $query = RestaurantTable::query();

        if ($search = $request->get('search')) {
            $query->where('table_number', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $tables = $query->orderBy('table_number')->paginate(10);

        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        return view('admin.tables.form', ['table' => new RestaurantTable]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:10|unique:restaurant_tables,table_number',
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        RestaurantTable::create($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil ditambahkan.');
    }

    public function edit(RestaurantTable $table)
    {
        return view('admin.tables.form', compact('table'));
    }

    public function update(Request $request, RestaurantTable $table)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|max:10|unique:restaurant_tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
            'status' => 'required|in:available,occupied,reserved,maintenance',
        ]);

        $table->update($validated);

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(RestaurantTable $table)
    {
        if ($table->orders()->count() > 0) {
            return redirect()->route('admin.tables.index')
                ->with('error', 'Meja tidak bisa dihapus karena memiliki riwayat pesanan.');
        }

        $table->delete();

        return redirect()->route('admin.tables.index')
            ->with('success', 'Meja berhasil dihapus.');
    }
}

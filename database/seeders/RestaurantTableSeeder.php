<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['table_number' => 'A1', 'capacity' => 2],
            ['table_number' => 'A2', 'capacity' => 2],
            ['table_number' => 'A3', 'capacity' => 4],
            ['table_number' => 'A4', 'capacity' => 4],
            ['table_number' => 'B1', 'capacity' => 4],
            ['table_number' => 'B2', 'capacity' => 4],
            ['table_number' => 'B3', 'capacity' => 6],
            ['table_number' => 'B4', 'capacity' => 6],
            ['table_number' => 'C1', 'capacity' => 2],
            ['table_number' => 'C2', 'capacity' => 2],
            ['table_number' => 'C3', 'capacity' => 4],
            ['table_number' => 'C4', 'capacity' => 4],
            ['table_number' => 'D1', 'capacity' => 6],
            ['table_number' => 'D2', 'capacity' => 8],
            ['table_number' => 'D3', 'capacity' => 8],
            ['table_number' => 'E1', 'capacity' => 2],
            ['table_number' => 'E2', 'capacity' => 2],
            ['table_number' => 'E3', 'capacity' => 4],
            ['table_number' => 'VIP1', 'capacity' => 6],
            ['table_number' => 'VIP2', 'capacity' => 10],
        ];

        foreach ($tables as $table) {
            RestaurantTable::create($table);
        }
    }
}

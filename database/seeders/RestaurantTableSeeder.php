<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 10) as $n) {
            $num = str_pad((string) $n, 2, '0', STR_PAD_LEFT);
            $code = 'A' . $num;

            RestaurantTable::updateOrCreate(
                ['qr_token' => 'table-' . strtolower($code)],
                [
                    'code' => $code,
                    'table_number' => $n,
                    'name' => 'Meja ' . $num,
                    'capacity' => 4,
                    'status' => 'available',
                ]
            );
        }
    }
}

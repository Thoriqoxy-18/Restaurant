<?php

namespace App\Console\Commands;

use App\Models\RestaurantTable;
use App\Support\DemoQrCode;
use Illuminate\Console\Command;

class GenerateTableQr extends Command
{
    protected $signature = 'qr:generate-tables';

    protected $description = 'Generate QR meja (PNG) ke public/qr-tables berdasarkan APP_URL saat ini';

    public function handle(): int
    {
        $dir = public_path('qr-tables');
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $tables = RestaurantTable::query()->orderBy('table_number')->get();

        if ($tables->isEmpty()) {
            $this->error('Tidak ada meja. Jalankan seeder terlebih dahulu.');

            return self::FAILURE;
        }

        foreach ($tables as $table) {
            $url = url('/menu?table='.$table->id);
            $png = DemoQrCode::png($url);
            $name = 'meja-'.str_pad((string) $table->table_number, 2, '0', STR_PAD_LEFT).'.png';
            file_put_contents($dir.DIRECTORY_SEPARATOR.$name, $png);
            $this->line("  {$name}  <-  {$url}");
        }

        $this->info('QR meja berhasil dibuat: '.public_path('qr-tables'));

        return self::SUCCESS;
    }
}

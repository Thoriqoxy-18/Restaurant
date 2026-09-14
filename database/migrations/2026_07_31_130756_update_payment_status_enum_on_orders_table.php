<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: tambah nilai enum. SQLite tidak punya enum asli (diperlakukan varchar),
        // jadi ALTER ini hanya untuk MySQL.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid','paid','waiting_verification','failed') NOT NULL DEFAULT 'unpaid'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid'");
        }
    }
};

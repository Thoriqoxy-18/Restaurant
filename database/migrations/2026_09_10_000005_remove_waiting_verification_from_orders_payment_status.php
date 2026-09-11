<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nilai enum waiting_verification/failed tidak pernah di-set oleh logika apa pun.
        // Bersihkan data lama yang masih memakai nilai tsb, lalu sederhanakan enum.
        DB::table('orders')
            ->whereIn('payment_status', ['waiting_verification', 'failed'])
            ->update(['payment_status' => 'unpaid']);

        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid','paid') NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY payment_status ENUM('unpaid','paid','waiting_verification','failed') NOT NULL DEFAULT 'unpaid'");
    }
};
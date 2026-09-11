<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index untuk kolom yang sering dipakai di WHERE/ORDER BY (dashboard,
        // daftar order, filter pembayaran) agar tidak full table scan.
        Schema::table('orders', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['payment_status']);
        });
    }
};
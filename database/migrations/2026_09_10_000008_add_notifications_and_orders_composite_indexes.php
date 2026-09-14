<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // notifications.latest() = ORDER BY created_at DESC (komposit read_at,created_at
        // tidak cocok untuk ordering ini). Tambah index created_at tersendiri.
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('created_at');
        });

        // Query pendapatan: where(payment_status=paid) + whereBetween(created_at).
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['payment_status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'created_at']);
        });
    }
};
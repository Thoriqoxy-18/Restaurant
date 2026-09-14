<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // order_items adalah tabel terbesar & sering di-scan untuk dashboard (sum quantity hari ini).
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
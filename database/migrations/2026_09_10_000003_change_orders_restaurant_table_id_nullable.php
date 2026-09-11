<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah cascadeOnDelete -> nullOnDelete agar menghapus meja TIDAK ikut
        // menghapus order (riwayat penjualan tetap tersimpan; restaurant_table_id jadi null).
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['restaurant_table_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurant_table_id')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('restaurant_table_id')->references('id')->on('restaurant_tables')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['restaurant_table_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('restaurant_table_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('restaurant_table_id')->references('id')->on('restaurant_tables')->cascadeOnDelete();
        });
    }
};
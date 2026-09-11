<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah cascadeOnDelete -> nullOnDelete agar menghapus menu tidak
        // menghapus riwayat order_items (riwayat penjualan tetap tersimpan).
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_item_id')->nullable()->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_item_id')->nullable(false)->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
        });
    }
};
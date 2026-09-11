<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Composite index untuk query menu customer yang memfilter is_available
        // lalu mengurutkan is_signature/is_bestseller.
        Schema::table('menu_items', function (Blueprint $table) {
            $table->index(['is_available', 'is_signature', 'is_bestseller'], 'menu_items_available_badges_index');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex('menu_items_available_badges_index');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // table_number harus unik (backstop race max()+1 di tableStore).
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->unique('table_number');
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropUnique('restaurant_tables_table_number_unique');
        });
    }
};
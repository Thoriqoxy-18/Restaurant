<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // session_token sudah UNIQUE (implicit index), index eksplisit redundant.
        Schema::table('customer_sessions', function (Blueprint $table) {
            $table->dropIndex('customer_sessions_session_token_index');
        });
    }

    public function down(): void
    {
        Schema::table('customer_sessions', function (Blueprint $table) {
            $table->index('session_token');
        });
    }
};
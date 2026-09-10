<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('qr_token', 64)->unique();
            $table->integer('capacity')->default(4);
            $table->enum('status', ['available','occupied','reserved'])->default('available');
            $table->timestamps();
            $table->index('qr_token');
        });
    }
    public function down(): void { Schema::dropIfExists('restaurant_tables'); }
};

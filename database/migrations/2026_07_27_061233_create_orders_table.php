<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('restaurant_table_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('kasir_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending','confirmed','preparing','served','completed','cancelled'])->default('pending');
            $table->enum('payment_status', ['unpaid','paid'])->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('service_charge', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['status', 'restaurant_table_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

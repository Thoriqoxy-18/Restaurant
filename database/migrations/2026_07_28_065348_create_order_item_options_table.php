<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['variation','topping','sauce']);
            $table->string('name');
            $table->decimal('extra_price', 12, 2)->default(0);
            $table->timestamps();
            $table->index('order_item_id');
        });
    }
    public function down(): void { Schema::dropIfExists('order_item_options'); }
};

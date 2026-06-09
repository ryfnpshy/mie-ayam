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
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_wa');
            $table->text('customer_address');
            $table->decimal('distance_km', 8, 2);
            $table->unsignedInteger('shipping_cost');
            $table->unsignedInteger('total_price');
            $table->string('status')->default('pending'); // pending, paid, processing, shipped, completed
            $table->string('payment_proof_status')->default('unpaid'); // unpaid, paid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

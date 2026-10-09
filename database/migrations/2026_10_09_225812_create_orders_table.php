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

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('order_number')->unique();
            $table->string('status')->default('pending_payment');

            // Delivery information saved with the order
            $table->string('recipient_name');
            $table->string('phone');
            $table->string('address_line');
            $table->string('barangay');
            $table->string('city');
            $table->string('province');
            $table->string('postal_code')->nullable();
            $table->text('notes')->nullable();

            // Order totals
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->constrained()->onDelete('restrict');

            // Pricing
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Payment
            $table->enum('payment_method', ['cash', 'wallet', 'card'])->default('cash');
            $table->string('stripe_payment_intent_id')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->timestamp('paid_at')->nullable();

            // Order Status
            $table->enum('status', [
                'pending',
                'confirmed',
                'processing',
                'completed',
                'cancelled'
            ])->default('pending');

            // Optional: Coupon
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();

            // Status timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'status']);
            $table->index('order_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

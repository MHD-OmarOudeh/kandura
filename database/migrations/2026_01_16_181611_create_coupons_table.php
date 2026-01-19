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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description')->nullable();

            // Discount type and value
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2); // 5 for 5% OR 5.00 SAR

            // Usage limits
            $table->integer('max_uses')->nullable(); // NULL = unlimited
            $table->integer('used_count')->default(0);
            $table->integer('max_uses_per_user')->default(1);

            // Validity period
            $table->timestamp('starts_at')->nullable(); // NULL = starts now
            $table->timestamp('expires_at');

            // Minimum order amount (optional)
            $table->decimal('min_order_amount', 10, 2)->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('is_active');
            $table->index(['starts_at', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

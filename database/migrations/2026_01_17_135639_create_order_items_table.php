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
        Schema::create('order_items', function (Blueprint $table) {
             $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('design_id')->constrained()->onDelete('restrict');

            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);

            // Store snapshot of design details at time of order
            $table->json('design_snapshot')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'design_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

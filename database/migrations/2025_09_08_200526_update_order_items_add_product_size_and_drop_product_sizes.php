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
        // Add product_size column to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('product_size', ['P', 'M', 'G', 'GG', 'XG', 'XGG', 'XXGG'])->nullable()->after('variation_id');
        });

        // Drop the product_sizes table
        Schema::dropIfExists('product_sizes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove product_size column from order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_size');
        });

        // Recreate the product_sizes table
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('tamanho', ['PP', 'P', 'M', 'G', 'GG', 'XGG', 'XXGG']);
            $table->timestamps();
        });
    }
};
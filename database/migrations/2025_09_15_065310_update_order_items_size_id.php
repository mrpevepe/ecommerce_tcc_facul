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
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_size');
            $table->foreignId('size_id')->nullable()->after('variation_id')->constrained('sizes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['size_id']);
            $table->dropColumn('size_id');
            $table->enum('product_size', ['P', 'M', 'G', 'GG', 'XG', 'XGG', 'XXGG'])->nullable()->after('variation_id');
        });
    }
};
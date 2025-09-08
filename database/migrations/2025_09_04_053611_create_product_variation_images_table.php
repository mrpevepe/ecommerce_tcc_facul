<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variation_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variation_id')->constrained('product_variations')->onDelete('cascade');
            $table->string('path'); // caminho da imagem da variação
            $table->boolean('is_main')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variation_images');
    }
};

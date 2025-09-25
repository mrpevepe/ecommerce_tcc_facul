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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
            $table->string('logradouro');
            $table->string('numero');
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cep', 9);
            $table->string('nome_cidade');
            $table->string('estado', 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('address_id')->nullable();
            $table->foreign('address_id')->references('id')->on('enderecos')->onDelete('cascade');
            $table->dropColumn(['logradouro', 'numero', 'complemento', 'bairro', 'cep', 'nome_cidade', 'estado']);
        });
    }
};
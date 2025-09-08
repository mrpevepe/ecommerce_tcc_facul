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

            // Usuário que fez o pedido
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Endereço de entrega (pode ser diferente do cadastrado no user)
            $table->foreignId('address_id')->constrained('enderecos')->onDelete('cascade');

            // Status do pedido
            $table->enum('status', [
                'pending',    // aguardando pagamento
                'paid',       // pago
                'preparing',  // em preparo
                'shipped',    // enviado
                'delivered',  // entregue/concluído
                'cancelled',  // cancelado
            ])->default('pending');

            // Informações de pagamento
            $table->string('payment_id')->nullable(); // ID da transação no MercadoPago
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable(); // status retornado pela API
            $table->decimal('total_price', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
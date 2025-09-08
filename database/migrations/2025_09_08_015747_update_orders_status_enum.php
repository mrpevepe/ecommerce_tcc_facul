<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateOrdersStatusEnum extends Migration
{
    public function up()
    {
        // Atualiza o ENUM para conter apenas os valores desejados
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'cancelled', 'delivered') NOT NULL DEFAULT 'pending'");
    }

    public function down()
    {
        // Reverte para o ENUM original, caso necessário
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'preparing', 'paid', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Numéro et client
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');

            // Infos client recopiées
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->string('customer_company')->nullable();

            // Montant
            $table->unsignedBigInteger('total_amount');

            // Statuts
            $table->string('status')->default('pending');         // pending, confirmed, etc.
            $table->string('payment_status')->default('pending'); // pending, paid, failed

            // Paiement
            $table->string('payment_method')->nullable();         // orange_money, moov_money, bank_transfer, cash_on_delivery
            $table->string('payment_phone')->nullable();
            $table->string('payment_reference')->nullable();

            // Livraison
            $table->string('delivery_address');
            $table->string('delivery_city');
            $table->string('delivery_phone');

            // Divers
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

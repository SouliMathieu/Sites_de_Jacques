<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->string('payment_phone')->nullable()->after('payment_reference');
            $table->text('delivery_address')->nullable()->after('notes');
            $table->string('delivery_city')->nullable()->after('delivery_address');
            $table->string('delivery_phone')->nullable()->after('delivery_city');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'payment_phone', 'delivery_address', 'delivery_city', 'delivery_phone']);
        });
    }
};

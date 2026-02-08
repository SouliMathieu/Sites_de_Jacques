<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Champs déjà présents dans create_orders_table : rien à ajouter.
    }

    public function down()
    {
        // Rien à supprimer.
    }
};

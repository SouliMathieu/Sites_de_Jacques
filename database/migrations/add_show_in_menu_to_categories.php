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
        Schema::table('categories', function (Blueprint $table) {
            // Ajouter la colonne show_in_menu si elle n'existe pas
            if (!Schema::hasColumn('categories', 'show_in_menu')) {
                $table->boolean('show_in_menu')->default(true)->after('is_featured');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'show_in_menu')) {
                $table->dropColumn('show_in_menu');
            }
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdCampaignsTable extends Migration
{
    public function up()
    {
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('platform', ['google_ads', 'meta_ads', 'both'])->default('both');
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->json('product_ids'); // IDs des produits ciblés
            $table->decimal('budget', 10, 2);
            $table->integer('duration_days');
            $table->json('target_audience')->nullable(); // Critères de ciblage
            $table->text('ad_copy')->nullable();
            $table->string('campaign_id_google')->nullable(); // ID de campagne Google
            $table->string('campaign_id_meta')->nullable(); // ID de campagne Meta
            $table->json('performance_data')->nullable(); // Statistiques
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ad_campaigns');
    }
}

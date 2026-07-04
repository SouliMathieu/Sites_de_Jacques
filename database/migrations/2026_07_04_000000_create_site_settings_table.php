<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, image, textarea, url
            $table->string('group')->default('general'); // general, contact, social, slider, about
            $table->string('label')->nullable();
            $table->timestamps();
        });

        // Valeurs par défaut
        $settings = [
            // Contact
            ['key' => 'phone', 'value' => '+226 77 12 65 19', 'type' => 'text', 'group' => 'contact', 'label' => 'Téléphone'],
            ['key' => 'whatsapp', 'value' => '22663952032', 'type' => 'text', 'group' => 'contact', 'label' => 'Numéro WhatsApp (sans +)'],
            ['key' => 'email', 'value' => 'info@jacksonenergy.bf', 'type' => 'text', 'group' => 'contact', 'label' => 'Email'],
            ['key' => 'address', 'value' => 'Ouagadougou, Burkina Faso', 'type' => 'text', 'group' => 'contact', 'label' => 'Adresse'],
            ['key' => 'opening_hours', 'value' => 'Lun - Sam : 08h00 - 18h00', 'type' => 'text', 'group' => 'contact', 'label' => "Horaires d'ouverture"],
            ['key' => 'maps_embed', 'value' => '', 'type' => 'textarea', 'group' => 'contact', 'label' => 'Lien Google Maps (embed URL)'],

            // Réseaux sociaux
            ['key' => 'facebook_url', 'value' => '#', 'type' => 'url', 'group' => 'social', 'label' => 'Facebook URL'],
            ['key' => 'instagram_url', 'value' => '#', 'type' => 'url', 'group' => 'social', 'label' => 'Instagram URL'],
            ['key' => 'twitter_url', 'value' => '#', 'type' => 'url', 'group' => 'social', 'label' => 'Twitter/X URL'],

            // À propos & Slogan
            ['key' => 'about_title', 'value' => 'Jackson Energy International', 'type' => 'text', 'group' => 'about', 'label' => 'Titre À propos'],
            ['key' => 'about_text', 'value' => "Jackson Energy est une entreprise burkinabè spécialisée dans la fourniture et l'installation d'équipements solaires adaptés, pour les ménages et PME au Burkina Faso.", 'type' => 'textarea', 'group' => 'about', 'label' => 'Texte À propos'],
            ['key' => 'slogan', 'value' => 'Solutions Solaires au Burkina Faso', 'type' => 'text', 'group' => 'about', 'label' => 'Slogan'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
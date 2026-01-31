@extends('layouts.public')

@section('title', 'Contact - Grossiste Ouaga International')
@section('description', 'Contactez-nous pour vos besoins en énergie solaire et électronique au Burkina Faso. Devis gratuit et conseil expert.')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <nav class="text-sm text-gray-600 mb-4">
            <a href="{{ route('home') }}" class="hover:text-vert-energie">Accueil</a>
            <span class="mx-2">/</span>
            <span>Contact</span>
        </nav>
        
        <h1 class="text-3xl md:text-4xl font-montserrat font-bold text-gris-moderne mb-4">
            Contactez-nous
        </h1>
        <p class="text-gray-600 text-lg">
            Notre équipe d'experts est à votre disposition pour vous conseiller
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid lg:grid-cols-2 gap-12">
        <!-- Informations de contact -->
        <div>
            <h2 class="text-2xl font-montserrat font-bold text-gris-moderne mb-8">
                Nos coordonnées
            </h2>
            
            <div class="space-y-6">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-vert-energie rounded-lg flex items-center justify-center text-white text-xl">
                        📞
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Téléphone</h3>
                        <p class="text-gray-600 mb-2">Appelez-nous directement</p>
                        <a href="tel:+22665033700" class="text-vert-energie font-medium hover:underline text-lg">
                            +226 65 03 37 00
                        </a>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center text-white text-xl">
                        💬
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">WhatsApp</h3>
                        <p class="text-gray-600 mb-2">Chat instantané avec nos experts</p>
                        <a href="https://wa.me/22665033700" class="text-green-500 font-medium hover:underline text-lg">
                            +226 65 03 37 00
                        </a>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-orange-burkina rounded-lg flex items-center justify-center text-white text-xl">
                        📧
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Email</h3>
                        <p class="text-gray-600 mb-2">Envoyez-nous un message</p>
                        <a href="mailto:grossisteouagainternational@gmail.com" class="text-orange-burkina font-medium hover:underline">
                            grossisteouagainternational@gmail.com
                        </a>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-bleu-tech rounded-lg flex items-center justify-center text-white text-xl">
                        🏦
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Comptes bancaires</h3>
                        <p class="text-gray-600 mb-2">Pour vos virements</p>
                        <div class="space-y-1">
                            <p class="text-bleu-tech font-medium">UBA: 410730007217</p>
                            <p class="text-bleu-tech font-medium">Moov Money: 70103993</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center text-white text-xl">
                        🕒
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg mb-1">Horaires</h3>
                        <p class="text-gray-600">
                            Lundi - Samedi : 8h00 - 18h00<br>
                            Dimanche : Fermé
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Boutons d'action rapide -->
            <div class="mt-8 space-y-4">
                <a href="tel:+22665033700" 
                   class="w-full bg-orange-burkina text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-orange-600 transition flex items-center justify-center">
                    📞 +226 65 03 37 00
                </a>
                
                <a href="https://wa.me/22665033700?text=Bonjour, j'aimerais avoir des informations sur vos produits solaires" 
                   class="w-full bg-green-500 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-green-600 transition flex items-center justify-center">
                    💬 WhatsApp (+226 65 03 37 00)
                </a>
            </div>
        </div>

        <!-- Formulaire de contact -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-montserrat font-bold text-gris-moderne mb-6">
                Demande de devis
            </h2>
            
            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                        <input type="text" id="name" name="name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                        <input type="tel" id="phone" name="phone" required placeholder="+226 XX XX XX XX"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie focus:border-transparent">
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie focus:border-transparent">
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Sujet *</label>
                    <select id="subject" name="subject" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie focus:border-transparent">
                        <option value="">Sélectionnez un sujet</option>
                        <option value="devis">Demande de devis</option>
                        <option value="installation">Installation solaire</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="conseil">Conseil technique</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                    <textarea id="message" name="message" rows="5" required placeholder="Décrivez votre projet ou votre besoin..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie focus:border-transparent"></textarea>
                </div>
                
                <button type="submit" class="w-full bg-vert-energie text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-green-700 transition">
                    📨 Envoyer ma demande
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Section Call to Action -->
<section class="py-16 bg-gradient-cta text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-montserrat font-bold mb-6">
            Besoin d'un conseil immédiat ?
        </h2>
        <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto">
            Nos experts sont disponibles pour répondre à toutes vos questions sur l'énergie solaire
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="tel:+22665033700" class="bg-white text-orange-burkina px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-all hover-lift">
                📞 +226 65 03 37 00
            </a>
            <a href="https://wa.me/22665033700?text=Bonjour, j'ai besoin d'un conseil pour mon projet solaire" class="bg-green-500 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-green-600 transition-all hover-lift">
                💬 WhatsApp
            </a>
        </div>
    </div>
</section>
@endsection

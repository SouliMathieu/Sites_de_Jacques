@extends('layouts.app')

@section('content')

    {{-- CAROUSEL PLEINE LARGEUR --}}
    <section class="relative w-full">
        <div id="carousel" class="relative w-full h-[500px] sm:h-[600px] lg:h-[700px] overflow-hidden">

            {{-- Slide 1 : Panneau --}}
            <div class="carousel-slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out opacity-100">
                <img src="{{ asset('images/carousel/panneau.png') }}"
                     alt="Installation panneaux solaires"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="text-center text-white px-4 max-w-4xl">
                        <h1 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold mb-4">
                            Solutions Solaires Fiables
                        </h1>
                        <p class="text-base sm:text-lg lg:text-xl mb-6">
                            Autonomie énergétique pour votre maison ou entreprise
                        </p>
                        <div class="flex flex-wrap gap-3 justify-center">
                            <a href="tel:+22677126519"
                               class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold py-3 px-6 rounded-lg shadow-lg text-sm sm:text-base">
                                📞 Appelez +226 77 12 65 19
                            </a>
                            <a href="https://wa.me/22663952032"
                               class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg text-sm sm:text-base">
                                💬 WhatsApp +226 63 95 20 32
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 2 : Batterie --}}
            <div class="carousel-slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="{{ asset('images/carousel/batterie.png') }}"
                     alt="Batteries solaires"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="text-center text-white px-4 max-w-4xl">
                        <h2 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold mb-4">
                            Stockage d'Énergie Performant
                        </h2>
                        <p class="text-base sm:text-lg lg:text-xl mb-6">
                            Batteries durables pour une réserve d'énergie stable
                        </p>
                        <div class="flex flex-wrap gap-3 justify-center">
                            <a href="tel:+22677126519"
                               class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold py-3 px-6 rounded-lg shadow-lg text-sm sm:text-base">
                                Devis Batterie
                            </a>
                            <a href="https://wa.me/22663952032"
                               class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg text-sm sm:text-base">
                                Contact WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Slide 3 : Frigo / Équipements --}}
            <div class="carousel-slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="{{ asset('images/carousel/frigo.png') }}"
                     alt="Équipements solaires"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <div class="text-center text-white px-4 max-w-4xl">
                        <h2 class="text-3xl sm:text-4xl lg:text-6xl font-extrabold mb-4">
                            Alimentez Tous Vos Équipements
                        </h2>
                        <p class="text-base sm:text-lg lg:text-xl mb-6">
                            Frigos, éclairage, TV : énergie propre au quotidien
                        </p>
                        <div class="flex flex-wrap gap-3 justify-center">
                            <a href="{{ route('products.index') }}"
                               class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold py-3 px-6 rounded-lg shadow-lg text-sm sm:text-base">
                                Voir nos produits
                            </a>
                            <a href="https://wa.me/22663952032"
                               class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg shadow-lg text-sm sm:text-base">
                                Demander conseil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boutons de navigation --}}
            <button id="carousel-prev"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white rounded-full w-12 h-12 flex items-center justify-center text-3xl font-bold shadow-lg transition z-10">
                ‹
            </button>
            <button id="carousel-next"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white rounded-full w-12 h-12 flex items-center justify-center text-3xl font-bold shadow-lg transition z-10">
                ›
            </button>

            {{-- Indicateurs (points) --}}
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                <button class="carousel-indicator w-3 h-3 rounded-full bg-white/60 hover:bg-white transition" data-slide="0"></button>
                <button class="carousel-indicator w-3 h-3 rounded-full bg-white/60 hover:bg-white transition" data-slide="1"></button>
                <button class="carousel-indicator w-3 h-3 rounded-full bg-white/60 hover:bg-white transition" data-slide="2"></button>
            </div>
        </div>
    </section>

    {{-- SECTION PRÉSENTATION --}}
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-6 text-center">
                JACKSON ENERGY : Votre partenaire en énergie solaire
            </h2>
            
            <p class="text-gray-700 mb-4 text-center max-w-4xl mx-auto">
                Jackson Energy s'engage à fournir des solutions énergétiques durables, accessibles et innovantes pour accompagner la transition énergétique au Burkina Faso.
                Nous voulons offrir à nos clients des produits fiables, performants et un accompagnement personnalisé pour un meilleur accès à l'énergie propre.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                <div class="bg-gray-50 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-3">☀️</div>
                    <h3 class="font-bold text-lg mb-2">Panneaux Solaires</h3>
                    <p class="text-gray-600 text-sm">
                        Des panneaux adaptés à tous les projets résidentiels et commerciaux.
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-3">🔋</div>
                    <h3 class="font-bold text-lg mb-2">Batteries Performantes</h3>
                    <p class="text-gray-600 text-sm">
                        Solutions performantes pour une autonomie durable.
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-3">⚡</div>
                    <h3 class="font-bold text-lg mb-2">Installations Fiables</h3>
                    <p class="text-gray-600 text-sm">
                        Technologie fiable pour optimiser votre installation.
                    </p>
                </div>
            </div>

            <div class="mt-10 text-center">
                <p class="text-gray-700 mb-4">
                    Contactez-nous dès aujourd'hui pour un diagnostic gratuit et un devis personnalisé adaptés à vos besoins énergétiques.
                </p>
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="tel:+22677126519" 
                       class="bg-amber-500 hover:bg-amber-600 text-gray-900 font-bold py-3 px-6 rounded-lg shadow">
                        📞 Appelez-nous
                    </a>
                    <a href="https://wa.me/22663952032" 
                       class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-lg shadow">
                        💬 WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

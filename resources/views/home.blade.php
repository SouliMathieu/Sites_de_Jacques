@extends('layouts.app')

@section('content')

    <!-- Carousel d'images -->
    <div class="w-full relative mb-8">
        <div id="carousel" class="relative h-[400px] md:h-[500px] overflow-hidden rounded-lg shadow-lg">
            <div class="carousel-slide absolute w-full h-full opacity-100 transition-all duration-700 ease-in-out">
                <img src="{{ asset('images/carousel/panneau.jpg') }}" alt="Énergie solaire 1" class="w-full h-full object-cover" />
            </div>
            <div class="carousel-slide absolute w-full h-full opacity-0 transition-all duration-700 ease-in-out">
                <img src="{{ asset('images/carousel/frigo.jpg') }}" alt="Énergie solaire 2" class="w-full h-full object-cover" />
            </div>
            <div class="carousel-slide absolute w-full h-full opacity-0 transition-all duration-700 ease-in-out">
                <img src="{{ asset('images/carousel/batterie.jpg') }}" alt="Énergie solaire 3" class="w-full h-full object-cover" />
            </div>
            <button type="button" id="carousel-prev" class="absolute left-3 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white px-2 py-1 rounded-full shadow z-10">&#10094;</button>
            <button type="button" id="carousel-next" class="absolute right-3 top-1/2 -translate-y-1/2 bg-white/70 hover:bg-white px-2 py-1 rounded-full shadow z-10">&#10095;</button>
        </div>
    </div>

    <!-- Mission entreprise -->
    <section class="max-w-4xl mx-auto my-12 px-4 text-center">
        <h2 class="text-3xl font-bold text-vert-burkina mb-4">Notre mission</h2>
        <p class="text-lg text-gray-700 leading-relaxed">
            Jackson Energy s'engage à fournir des solutions énergétiques durables, accessibles et innovantes pour accompagner la transition énergétique au Burkina Faso. Nous voulons offrir à nos clients des produits fiables, performants et un accompagnement personnalisé pour un meilleur accès à l'énergie propre.
        </p>
    </section>

    <!-- Nos Catégories -->
    <section class="bg-vert-burkina bg-opacity-10 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h3 class="text-2xl font-bold text-vert-burkina mb-8 text-center">Nos Catégories</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-6 hover-lift transition-all cursor-pointer">
                    <h4 class="text-xl font-semibold mb-2">Panneaux solaires</h4>
                    <p class="text-gray-600">Des panneaux adaptés à tous les projets résidentiels et commerciaux.</p>
                </div>
                <div class="glass-card p-6 hover-lift transition-all cursor-pointer">
                    <h4 class="text-xl font-semibold mb-2">Batteries et Stockage</h4>
                    <p class="text-gray-600">Solutions performantes pour une autonomie durable.</p>
                </div>
                <div class="glass-card p-6 hover-lift transition-all cursor-pointer">
                    <h4 class="text-xl font-semibold mb-2">Onduleurs & Régulateurs</h4>
                    <p class="text-gray-600">Technologie fiable pour optimiser votre installation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pourquoi nous choisir ? -->
    <section class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl font-bold text-rouge-burkina mb-8">Pourquoi nous choisir ?</h3>
            <ul class="max-w-3xl mx-auto space-y-6 text-gray-700 list-disc list-inside text-left">
                <li>Plus de 10 ans d’expérience dans le solaire au Burkina Faso.</li>
                <li>Produits certifiés, garantis et conformes aux normes internationales.</li>
                <li>Livraison rapide dans tout le pays, service client réactif.</li>
                <li>Conseil technique personnalisé et accompagnement de projet.</li>
                <li>Engagement fort pour un avenir énergétique durable et accessible.</li>
            </ul>
        </div>
    </section>

    <!-- Prêt à passer à l'énergie solaire ? -->
    <section class="bg-gradient-to-r from-rouge-burkina via-jaune-burkina to-vert-burkina py-12 text-white text-center rounded-lg mx-4 md:mx-12">
        <h3 class="text-2xl font-extrabold mb-4">Prêt à passer à l'énergie solaire ?</h3>
        <p class="mb-6 max-w-2xl mx-auto text-lg">Contactez-nous dès aujourd'hui pour un diagnostic gratuit et un devis personnalisé adaptés à vos besoins énergétiques.</p>
        <a href="tel:+22677126519" class="btn-primary inline-block px-8 py-3 font-bold">Appelez-nous</a>
        <a href="https://wa.me/22663952032" target="_blank" class="btn-secondary inline-block px-8 py-3 font-bold ml-4">WhatsApp</a>
    </section>

    <!-- Pied de page -->
    <footer class="bg-gris-moderne text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h4 class="text-lg font-semibold mb-4">Contactez-nous</h4>
                <p>Téléphone : +226 77 12 65 19</p>
                <p>WhatsApp : +226 63 95 20 32</p>
                <p>Email : info@jacksonenergy.bf</p>
                <div class="mt-4">
                    <h5 class="font-semibold mb-2">Notre localisation</h5>
                    <iframe class="w-full h-40 rounded" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3979.847240514565!2d-1.5212256!3d12.3714323!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfad6eff7fd1641ef%3A0xe991a83d9b374264!2sOuagadougou!5e0!3m2!1sfr!2sbf!4v1615149550971!5m2!1sfr!2sbf" loading="lazy"></iframe>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Liens rapides</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('home') }}" class="hover:underline">Accueil</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:underline">Produits</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:underline">Contact</a></li>
                    <!-- Suppression du lien "À propos" pour éviter l'erreur -->
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4">Suivez-nous</h4>
                <div class="flex space-x-4">
                    <a href="#" class="hover:text-yellow-400" aria-label="Facebook">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12a10 10 0 0010 10v-7h-3v-3h3v-2c0-2.76 2.24-5 5-5h2v3h-2c-.55 0-1 .45-1 1v2h3l-1 3h-2v7a10 10 0 0010-10z"/>
                        </svg>
                    </a>
                    <!-- Ajoute autres réseaux sociaux si nécessaire -->
                </div>
            </div>
        </div>
        <div class="mt-8 text-center text-gray-400 text-sm">
            &copy; 2025 Jackson Energy. Tous droits réservés.
        </div>
    </footer>

@endsection

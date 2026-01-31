@extends('layouts.public')

@section('title', 'Accueil - Grossiste Ouaga International')

@section('content')
<!-- Hero Section avec gradient et animation -->
<section class="bg-gradient-hero text-white py-20 relative overflow-hidden">
    <!-- Effet de particules en arrière-plan -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-20 h-20 bg-white rounded-full animate-pulse"></div>
        <div class="absolute top-32 right-20 w-16 h-16 bg-orange-burkina rounded-full animate-bounce"></div>
        <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-white rounded-full animate-ping"></div>
        <div class="absolute bottom-32 right-1/3 w-8 h-8 bg-orange-burkina rounded-full animate-pulse"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center animate-fade-in-up">
            <h1 class="text-4xl md:text-6xl font-montserrat font-black mb-6 typing-effect">
                Énergie Solaire & Électronique
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90 font-light">
                Solutions durables et innovantes pour le <span class="text-orange-burkina font-semibold">Burkina Faso</span>
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="tel:+22665033700" class="btn-secondary hover-lift transition-all">
                    📞 Appelez maintenant
                </a>
                <a href="https://wa.me/22665033700" class="bg-green-500 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-green-600 transition-all hover-lift">
                    💬 WhatsApp
                </a>
                <a href="{{ route('products.index') }}" class="bg-white text-vert-energie px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-all hover-lift">
                    🛍️ Voir les produits
                </a>
            </div>
        </div>
    </div>

    <!-- Vague décorative -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1200 120" preserveAspectRatio="none" class="relative block w-full h-16">
            <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="#f8fafc"></path>
        </svg>
    </div>
</section>

<!-- Catégories principales avec effet glassmorphism -->
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-montserrat font-bold text-center mb-4 text-gris-moderne">
            Nos Catégories
        </h2>
        <p class="text-center text-gray-600 mb-12 text-lg">
            Découvrez notre gamme complète de solutions énergétiques
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="glass-card rounded-xl p-8 text-center hover-lift transition-all group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-all">☀️</div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Panneaux Solaires</h3>
                <p class="text-gray-600 mb-4">Résidentiels et commerciaux de haute qualité</p>
                <div class="w-full h-1 bg-gradient-hero rounded-full mb-4"></div>
                <a href="{{ route('products.index', ['category' => 'panneaux-solaires']) }}" class="text-vert-energie font-semibold hover:text-orange-burkina transition-all">
                    Voir les produits →
                </a>
            </div>

            <div class="glass-card rounded-xl p-8 text-center hover-lift transition-all group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-all">🔋</div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Batteries</h3>
                <p class="text-gray-600 mb-4">Gel, Lithium, AGM pour tous besoins</p>
                <div class="w-full h-1 bg-gradient-cta rounded-full mb-4"></div>
                <a href="{{ route('products.index', ['category' => 'batteries']) }}" class="text-vert-energie font-semibold hover:text-orange-burkina transition-all">
                    Voir les produits →
                </a>
            </div>

            <div class="glass-card rounded-xl p-8 text-center hover-lift transition-all group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-all">⚡</div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Onduleurs</h3>
                <p class="text-gray-600 mb-4">Convertisseurs et régulateurs de qualité</p>
                <div class="w-full h-1 bg-gradient-hero rounded-full mb-4"></div>
                <a href="{{ route('products.index', ['category' => 'onduleurs']) }}" class="text-vert-energie font-semibold hover:text-orange-burkina transition-all">
                    Voir les produits →
                </a>
            </div>

            <div class="glass-card rounded-xl p-8 text-center hover-lift transition-all group">
                <div class="text-5xl mb-4 group-hover:scale-110 transition-all">📱</div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Électronique</h3>
                <p class="text-gray-600 mb-4">Appareils basse consommation</p>
                <div class="w-full h-1 bg-gradient-cta rounded-full mb-4"></div>
                <a href="{{ route('products.index', ['category' => 'electronique']) }}" class="text-vert-energie font-semibold hover:text-orange-burkina transition-all">
                    Voir les produits →
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Section avantages avec design moderne -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-montserrat font-bold text-center mb-4 text-gris-moderne">
            Pourquoi nous choisir ?
        </h2>
        <p class="text-center text-gray-600 mb-12 text-lg">
            Votre partenaire de confiance pour l'énergie solaire au Burkina Faso
        </p>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-hero rounded-full flex items-center justify-center text-white text-3xl mx-auto mb-6 group-hover:scale-110 transition-all">
                    ✓
                </div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Qualité Garantie</h3>
                <p class="text-gray-600">Produits certifiés et garantis selon les standards internationaux</p>
            </div>

            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-cta rounded-full flex items-center justify-center text-white text-3xl mx-auto mb-6 group-hover:scale-110 transition-all">
                    🚚
                </div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Livraison Rapide</h3>
                <p class="text-gray-600">Livraison dans tout le Burkina Faso sous 48-72h</p>
            </div>

            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-hero rounded-full flex items-center justify-center text-white text-3xl mx-auto mb-6 group-hover:scale-110 transition-all">
                    💰
                </div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Prix Compétitifs</h3>
                <p class="text-gray-600">Meilleurs prix du marché avec des promotions régulières</p>
            </div>

            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-cta rounded-full flex items-center justify-center text-white text-3xl mx-auto mb-6 group-hover:scale-110 transition-all">
                    🎯
                </div>
                <h3 class="font-montserrat font-bold text-xl mb-3 text-gris-moderne">Support Expert</h3>
                <p class="text-gray-600">Conseil et assistance technique par nos experts</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action avec gradient -->
<!-- Call to Action avec gradient -->
<section class="py-20 bg-gradient-cta text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 right-10 w-32 h-32 bg-white rounded-full animate-pulse"></div>
        <div class="absolute bottom-10 left-10 w-24 h-24 bg-white rounded-full animate-bounce"></div>
    </div>

    <div class="container mx-auto px-4 text-center relative z-10">
        <h2 class="text-3xl md:text-4xl font-montserrat font-bold mb-6">
            Prêt à passer à l'énergie solaire ?
        </h2>
        <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto">
            Contactez-nous dès maintenant pour un devis personnalisé et découvrez comment économiser sur vos factures d'électricité
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="tel:+22665033700" class="bg-white text-orange-burkina px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-all hover-lift">
                📞 +226 65 03 37 00
            </a>
            <a href="https://wa.me/22665033700" class="bg-green-500 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-green-600 transition-all hover-lift">
                💬 WhatsApp
            </a>
        </div>
    </div>
</section>

@endsection

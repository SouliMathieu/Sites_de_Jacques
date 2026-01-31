@extends('layouts.public')

@section('title', 'Catégories - Grossiste Ouaga International')
@section('description', 'Explorez nos différentes catégories de produits solaires et électroniques au Burkina Faso.')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <nav class="text-sm text-gray-600 mb-4">
            <a href="{{ route('home') }}" class="hover:text-vert-energie">Accueil</a>
            <span class="mx-2">/</span>
            <span>Catégories</span>
        </nav>
        
        <h1 class="text-3xl md:text-4xl font-montserrat font-bold text-gris-moderne mb-4">
            Nos Catégories
        </h1>
        <p class="text-gray-600 text-lg">
            Découvrez notre gamme complète de produits organisés par catégories
        </p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($categories as $category)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
            <div class="relative">
                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-64 object-cover group-hover:scale-105 transition">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                <div class="absolute bottom-4 left-4 text-white">
                    <h3 class="text-2xl font-montserrat font-bold mb-2">{{ $category->name }}</h3>
                    <p class="text-sm opacity-90">{{ $category->products_count }} produit(s)</p>
                </div>
            </div>
            
            <div class="p-6">
                <p class="text-gray-600 mb-6 leading-relaxed">{{ $category->description }}</p>
                
                <div class="flex space-x-3">
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="flex-1 bg-vert-energie text-white text-center py-3 rounded-lg hover:bg-green-700 transition font-medium">
                        Voir les produits
                    </a>
                    <a href="https://wa.me/22670123456?text=Je suis intéressé par la catégorie {{ $category->name }}" 
                       class="bg-green-500 text-white px-4 py-3 rounded-lg hover:bg-green-600 transition">
                        💬
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Section avantages -->
<section class="py-16 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-montserrat font-bold text-center mb-12 text-gris-moderne">
            Pourquoi choisir nos produits ?
        </h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-vert-energie rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                    ⚡
                </div>
                <h3 class="font-montserrat font-semibold text-lg mb-2">Haute Performance</h3>
                <p class="text-gray-600">Produits de dernière génération avec rendement optimal</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-burkina rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                    🛡️
                </div>
                <h3 class="font-montserrat font-semibold text-lg mb-2">Garantie Étendue</h3>
                <p class="text-gray-600">Garantie constructeur jusqu'à 25 ans sur certains produits</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-bleu-tech rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                    🌍
                </div>
                <h3 class="font-montserrat font-semibold text-lg mb-2">Écologique</h3>
                <p class="text-gray-600">Solutions respectueuses de l'environnement</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                    💰
                </div>
                <h3 class="font-montserrat font-semibold text-lg mb-2">Économique</h3>
                <p class="text-gray-600">Réduction significative de vos factures d'électricité</p>
            </div>
        </div>
    </div>
</section>
@endsection

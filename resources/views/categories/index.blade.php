@extends('layouts.app', ['title' => 'Nos Catégories - Jackson Energy International', 'description' => 'Découvrez nos catégories de produits solaires : panneaux, batteries, onduleurs, régulateurs au Burkina Faso.'])

@section('content')
    {{-- Header de la page --}}
    <section class="bg-gradient-to-r from-blue-50 to-green-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-600 mb-4">
                <a href="{{ route('home') }}" class="hover:text-green-600 transition">Accueil</a>
                <span class="mx-2">/</span>
                <span class="text-green-600 font-semibold">Catégories</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Nos Catégories</h1>
            <p class="text-gray-600 text-base sm:text-lg">
                Découvrez notre gamme complète de produits organisés par catégories
            </p>
        </div>
    </section>

    {{-- Section Pourquoi choisir --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-center text-gray-900 mb-10">
                Pourquoi choisir nos produits ?
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Haute Performance --}}
                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-green-100 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <div class="bg-green-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Haute Performance</h3>
                    <p class="text-gray-600 text-sm">
                        Produits de dernière génération avec rendement optimal
                    </p>
                </div>

                {{-- Garantie Étendue --}}
                <div class="text-center p-6 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <div class="bg-orange-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Garantie Étendue</h3>
                    <p class="text-gray-600 text-sm">
                        Garantie constructeur jusqu'à 25 ans sur certains produits
                    </p>
                </div>

                {{-- Écologique --}}
                <div class="text-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <div class="bg-blue-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Écologique</h3>
                    <p class="text-gray-600 text-sm">
                        Solutions respectueuses de l'environnement
                    </p>
                </div>

                {{-- Économique --}}
                <div class="text-center p-6 bg-gradient-to-br from-green-50 to-emerald-100 rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <div class="bg-emerald-600 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900 mb-2">Économique</h3>
                    <p class="text-gray-600 text-sm">
                        Réduction significative de vos factures
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Liste des catégories --}}
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <a href="{{ route('categories.show', $category->id) }}" 
                           class="group bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                            @if($category->image)
                                <div class="h-48 overflow-hidden bg-gray-100">
                                    <img src="{{ asset('storage/' . $category->image) }}" 
                                         alt="{{ $category->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                                    <span class="text-white text-5xl font-bold">{{ substr($category->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ $category->description ?? 'Découvrez nos produits dans cette catégorie' }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                                        {{ $category->products_count ?? 0 }} produit(s)
                                    </span>
                                    <span class="inline-flex items-center text-green-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                        Voir
                                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($categories->hasPages())
                    <div class="mt-10">
                        {{ $categories->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12 bg-white rounded-lg">
                    <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucune catégorie disponible</h3>
                    <p class="text-gray-600 mb-6">Nos catégories seront bientôt disponibles</p>
                    <a href="{{ route('home') }}" 
                       class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition transform hover:scale-105">
                        Retour à l'accueil
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- CTA Contact --}}
    <section class="py-12 bg-gradient-to-r from-green-600 to-blue-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold mb-4">Besoin de conseils ?</h2>
            <p class="text-lg mb-6 text-blue-100">
                Nos experts sont à votre écoute pour vous guider dans le choix de vos équipements solaires
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="tel:+22677126519" 
                   class="bg-white text-green-600 hover:bg-gray-100 font-bold px-6 py-3 rounded-lg shadow-lg transition transform hover:scale-105">
                    📞 Appeler +226 77 12 65 19
                </a>
                <a href="https://wa.me/22663952032" 
                   target="_blank"
                   class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-6 py-3 rounded-lg shadow-lg transition transform hover:scale-105">
                    💬 WhatsApp +226 63 95 20 32
                </a>
            </div>
        </div>
    </section>
@endsection

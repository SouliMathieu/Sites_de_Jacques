@extends('layouts.app')

@section('title', 'Toutes les catégories - Jackson Energy International')

@section('content')
@php
    $phone = \App\Models\SiteSetting::get('phone', '+226 77 12 65 19');
    $whatsapp = \App\Models\SiteSetting::get('whatsapp', '22663952032');
    $telUrl = 'tel:' . preg_replace('/\s+/', '', $phone);
    $waUrl = 'https://wa.me/' . $whatsapp;
@endphp
<div class="bg-gradient-to-b from-gray-50 via-white to-gray-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Fil d’Ariane --}}
        <nav class="flex mb-6 text-sm text-gray-500" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-gray-500 hover:text-blue-600">
                        <span class="mr-1">🏠</span> Accueil
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-700 font-medium">Catégories</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold font-montserrat text-gray-900 tracking-tight flex items-center">
                    <span class="mr-3 text-3xl">📂</span>
                    Nos catégories de produits
                </h1>
                <p class="mt-3 text-base text-gray-600 max-w-2xl">
                    Découvrez notre gamme complète de produits organisés par catégories pour répondre à tous vos besoins en énergie et équipements.
                </p>
            </div>

            <div class="flex flex-col items-start md:items-end gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold uppercase tracking-wider">
                    🔋 Énergie solaire & équipements
                </span>
                <p class="text-xs text-gray-500">
                    Produits de dernière génération avec rendement optimal, installation professionnelle et suivi personnalisé.
                </p>
            </div>
        </div>

        {{-- Stats / Avantages --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-xl">⚡</div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Performance garantie</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Produits de dernière génération avec rendement optimal et certification internationale.
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-xl">🌱</div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Énergie propre</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Solutions respectueuses de l'environnement pour réduire durablement votre empreinte carbone.
                    </p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-xl">💰</div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Économies assurées</h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Réduction significative de vos factures d'électricité grâce à nos solutions solaires.
                    </p>
                </div>
            </div>
        </div>

        @if($categories->count())
            {{-- Grille des catégories --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($categories as $category)
                    <a href="{{ route('categories.show', $category->slug) }}"
                       class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <div class="relative h-40 bg-gray-100 overflow-hidden">
                            @if($category->image)
                                <img src="{{ $category->image_url }}"
                                     alt="{{ $category->name }}"
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50">
                                    <span class="text-4xl">📁</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/10 to-transparent opacity-60 group-hover:opacity-80 transition-opacity"></div>
                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-white/90 text-gray-800">
                                    {{ $category->products_count ?? $category->products()->count() }} produit(s)
                                </span>
                                @if($category->is_featured)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-yellow-400 text-yellow-900 shadow-sm">
                                        ⭐ Populaire
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="p-5">
                            <h2 class="text-base font-bold text-gray-900 mb-1 flex items-center">
                                <span class="mr-2 text-lg">🔹</span>
                                {{ $category->name }}
                            </h2>
                            <p class="text-xs text-gray-600 line-clamp-2 mb-3">
                                {{ $category->description ?? 'Découvrez nos produits dans cette catégorie' }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span class="inline-flex items-center">
                                    <span class="mr-1">➜</span>
                                    Voir les produits
                                </span>
                                <span class="inline-flex items-center">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2"></span>
                                    Disponible
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pas de pagination ici car $categories est une collection simple --}}
        @else
            {{-- Message si aucune catégorie --}}
            <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-200 py-16 px-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-3xl">
                        📂
                    </div>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    Aucune catégorie disponible pour le moment
                </h2>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                    Nos catégories seront bientôt disponibles. En attendant, vous pouvez parcourir nos produits principaux ou contacter notre équipe pour un accompagnement personnalisé.
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                        <span class="mr-2">🛒</span> Voir tous les produits
                    </a>
                    <a href="{{ route('home') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition-colors">
                        ← Retour à l'accueil
                    </a>
                </div>
            </div>
        @endif

        {{-- Bloc d’aide --}}
        <div class="mt-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl px-6 py-6 flex flex-col md:flex-row items-center justify-between text-white shadow-lg">
            <div class="flex items-center mb-4 md:mb-0">
                <div class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center mr-4 text-2xl">
                    ☎️
                </div>
                <div>
                    <h3 class="text-sm font-semibold">Besoin d’aide pour choisir la bonne catégorie ?</h3>
                    <p class="text-xs text-blue-100">
                        Nos experts sont à votre écoute pour vous guider dans le choix de vos équipements solaires.
                    </p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ $telUrl }}"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white text-blue-700 text-sm font-semibold shadow-sm hover:bg-blue-50 transition-colors">
                    <span class="mr-2">📞</span> Appeler un conseiller
                </a>
                <a href="{{ $waUrl }}"
                   target="_blank"
                   class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-emerald-500 text-white text-sm font-semibold shadow-sm hover:bg-emerald-400 transition-colors">
                    <span class="mr-2">💬</span> Discuter sur WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

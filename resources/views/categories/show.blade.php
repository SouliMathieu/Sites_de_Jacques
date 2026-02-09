@extends('layouts.app')

@section('title', $category->meta_title ?? ($category->name . ' - Jackson Energy International'))

@section('meta_description', $category->meta_description ?? Str::limit($category->description, 155))

@section('content')
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
                        <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-blue-600">
                            Catégories
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <span class="mx-2 text-gray-400">/</span>
                        <span class="text-gray-700 font-medium">{{ $category->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header catégorie --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
            <div class="grid md:grid-cols-3 gap-0">
                <div class="relative md:col-span-1 h-44 md:h-full bg-gray-100">
                    @if($category->image)
                        <img src="{{ $category->image_url }}"
                             alt="{{ $category->name }}"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50">
                            <span class="text-5xl">📂</span>
                        </div>
                    @endif
                    <div class="absolute bottom-3 left-3 flex items-center space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/90 text-gray-800 text-[11px] font-semibold">
                            {{ $category->products()->where('is_active', true)->count() }} produit(s)
                        </span>
                        @if($category->is_featured)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-yellow-400 text-yellow-900 text-[11px] font-semibold shadow-sm">
                                ⭐ Catégorie mise en avant
                            </span>
                        @endif
                    </div>
                </div>

                <div class="md:col-span-2 p-6 md:p-8 flex flex-col justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold font-montserrat text-gray-900 flex items-center">
                            <span class="mr-3 text-3xl">📂</span>
                            {{ $category->name }}
                        </h1>
                        <p class="mt-3 text-sm text-gray-600">
                            {{ $category->description ?? 'Découvrez nos produits dans cette catégorie' }}
                        </p>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-3 text-xs text-gray-500">
                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 font-semibold">
                            ⚡ Idéal pour vos projets solaires
                        </div>
                        <div class="inline-flex items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2"></span>
                            Produits en stock
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liste des produits de la catégorie --}}
        @if($products->count())
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $products->total() }} produit(s) dans cette catégorie
                </h2>
                <p class="text-xs text-gray-500">
                    Affichage de {{ $products->firstItem() }} à {{ $products->lastItem() }}
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach($products as $product)
                    <a href="{{ route('products.show', $product->slug) }}"
                       class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-200 transform hover:-translate-y-1">
                        <div class="relative h-44 bg-gray-100">
                            @if($product->first_image)
                                <img src="{{ $product->first_image }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                    <span class="text-4xl">📦</span>
                                </div>
                            @endif
                            @if($product->is_on_sale)
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-red-500 text-white text-[11px] font-semibold">
                                        🔥 PROMO -{{ $product->discount_percentage }}%
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="text-sm font-bold text-gray-900 line-clamp-2 mb-1">
                                {{ $product->name }}
                            </h3>
                            <p class="text-xs text-gray-500 line-clamp-2 mb-3">
                                {{ $product->short_description ?? Str::limit($product->description, 80) }}
                            </p>
                            <div class="flex items-end justify-between">
                                <div>
                                    @if($product->promotional_price && $product->promotional_price < $product->price)
                                        <div class="text-sm font-extrabold text-emerald-600">
                                            {{ number_format($product->promotional_price, 0, ',', ' ') }} F
                                        </div>
                                        <div class="text-[11px] text-gray-400 line-through">
                                            {{ number_format($product->price, 0, ',', ' ') }} F
                                        </div>
                                    @else
                                        <div class="text-sm font-extrabold text-gray-900">
                                            {{ number_format($product->price, 0, ',', ' ') }} F
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end text-[11px] text-gray-400">
                                    <span class="inline-flex items-center">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $product->is_in_stock ? 'bg-emerald-400' : 'bg-red-400' }} mr-2"></span>
                                        {{ $product->stock_status }}
                                    </span>
                                    <span class="mt-1">
                                        {{ $product->stock_quantity }} en stock
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        @else
            {{-- Aucun produit dans cette catégorie --}}
            <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-200 py-16 px-6 text-center">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-3xl">
                        📦
                    </div>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    Aucun produit dans cette catégorie pour le moment
                </h2>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto">
                    Revenez bientôt ou explorez nos autres catégories pour trouver le produit idéal pour votre projet.
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                        <span class="mr-2">🛒</span> Voir tous les produits
                    </a>
                    <a href="{{ route('categories.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition-colors">
                        ← Retour aux catégories
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app', ['title' => 'Nos Produits - Jackson Energy International', 'description' => 'Découvrez nos produits solaires : panneaux, batteries, onduleurs, régulateurs au Burkina Faso.'])

@section('content')
    {{-- Header de la page --}}
    <section class="bg-gradient-to-r from-blue-50 to-green-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-600 mb-4">
                <a href="{{ route('home') }}" class="hover:text-green-600 transition">Accueil</a>
                <span class="mx-2">/</span>
                <span class="text-blue-600 font-semibold">Produits</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-bold text-blue-600 mb-3">Nos Produits</h1>
            <p class="text-gray-600 text-base sm:text-lg">
                Solutions solaires complètes pour l'autonomie énergétique
            </p>
        </div>
    </section>

    {{-- Section produits --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                {{-- Sidebar filtres --}}
                <aside class="lg:w-1/4">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-24 shadow-md">
                        <h2 class="text-xl font-bold text-green-600 mb-4">Catégories</h2>
                        
                        <div class="space-y-2 mb-6">
                            <a href="{{ route('products.index') }}" 
                               class="block px-4 py-2 rounded-lg transition {{ !request('category') ? 'bg-green-50 text-green-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                <div class="flex justify-between items-center">
                                    <span>Tous les produits</span>
                                    @if(isset($totalProducts))
                                        <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded-full">{{ $totalProducts }}</span>
                                    @endif
                                </div>
                            </a>
                            
                            @foreach($categories ?? [] as $cat)
                                <a href="{{ route('products.index', ['category' => $cat->id]) }}" 
                                   class="block px-4 py-2 rounded-lg transition {{ request('category') == $cat->id ? 'bg-green-50 text-green-600 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                                    <div class="flex justify-between items-center">
                                        <span>{{ $cat->name }}</span>
                                        <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded-full">{{ $cat->products_count ?? 0 }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <hr class="my-6">

                        <h3 class="font-bold text-gray-900 mb-3">Recherche</h3>
                        <form action="{{ route('products.index') }}" method="GET" class="space-y-2">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Rechercher un produit..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm transition">
                            <button type="submit" 
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition transform hover:scale-105">
                                Rechercher
                            </button>
                        </form>
                    </div>
                </aside>

                {{-- Liste des produits --}}
                <div class="lg:w-3/4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                        <p class="text-gray-600">
                            <span class="font-semibold text-lg">{{ $products->total() ?? 0 }}</span> 
                            <span class="text-gray-500">produit(s) trouvé(s)</span>
                        </p>
                        <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            <label for="sort" class="text-sm text-gray-600 whitespace-nowrap">Trier par:</label>
                            <select name="sort" id="sort" onchange="this.form.submit()"
                                    class="flex-1 sm:flex-none border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 transition">
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nom A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nom Z-A</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            </select>
                        </form>
                    </div>

                    @if($products->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($products as $product)
                                <a href="{{ route('products.show', $product->slug) }}" 
                                   class="group bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden transform hover:-translate-y-1">
                                    @if($product->first_image)
                                        <div class="h-48 overflow-hidden bg-gray-100">
                                            <img src="{{ $product->first_image }}" 
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        </div>
                                    @else
                                        <div class="h-48 bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                                            <span class="text-white text-4xl">📦</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-green-600 transition">
                                            {{ $product->name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                            {{ Str::limit($product->description, 80) }}
                                        </p>
                                        @if($product->current_price)
                                            <div class="text-lg font-bold text-green-600 mb-3">
                                                {{ $product->formatted_promotional_price ?? $product->formatted_price }}
                                                @if($product->is_on_sale)
                                                    <span class="text-sm line-through text-gray-500">{{ $product->formatted_price }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        <span class="inline-flex items-center text-green-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                            Voir détails
                                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </span>
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
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun produit trouvé</h3>
                            <p class="text-gray-600 mb-6">Essayez de modifier vos critères de recherche</p>
                            <a href="{{ route('products.index') }}" 
                               class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition transform hover:scale-105">
                                Voir tous les produits
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Contact --}}
    <section class="py-12 bg-gradient-to-r from-green-600 to-blue-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold mb-4">Besoin d'un devis personnalisé ?</h2>
            <p class="text-lg mb-6 text-blue-100">
                Contactez Jackson Energy pour une étude gratuite de vos besoins énergétiques
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="tel:+22677126519" 
                   class="bg-white text-green-600 hover:bg-gray-100 font-bold px-6 py-3 rounded-lg shadow-lg transition transform hover:scale-105">
                    📞 Appeler maintenant
                </a>
                <a href="https://wa.me/22663952032" 
                   target="_blank"
                   class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold px-6 py-3 rounded-lg shadow-lg transition transform hover:scale-105">
                    💬 WhatsApp
                </a>
                <a href="{{ route('contact') }}" 
                   class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-6 py-3 rounded-lg shadow-lg transition transform hover:scale-105">
                    📧 Formulaire de contact
                </a>
            </div>
        </div>
    </section>
@endsection

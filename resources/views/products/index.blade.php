@extends('layouts.public')

@section('title', 'Nos Produits - Zida Solaire')
@section('description', 'Découvrez notre large gamme de produits solaires et électroniques au Burkina Faso.')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <nav class="text-sm text-gray-600 mb-4">
            <a href="{{ route('home') }}" class="hover:text-vert-energie">Accueil</a>
            <span class="mx-2">/</span>
            <span>Produits</span>
        </nav>

        <h1 class="text-3xl md:text-4xl font-montserrat font-bold text-gris-moderne mb-8">
            Nos Produits
        </h1>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filtres -->
        <div class="lg:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="font-montserrat font-semibold text-lg mb-4">Catégories</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between text-gray-600 hover:text-vert-energie {{ !request('category') ? 'text-vert-energie font-medium' : '' }}">
                            <span>Tous les produits</span>
                            <span class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $products->total() }}</span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                    <li>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="flex items-center justify-between text-gray-600 hover:text-vert-energie {{ request('category') == $category->slug ? 'text-vert-energie font-medium' : '' }}">
                            <span>{{ $category->name }}</span>
                            <span class="text-sm bg-gray-100 px-2 py-1 rounded">{{ $category->products_count }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>

            <!-- Recherche -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="font-montserrat font-semibold text-lg mb-4">Recherche</h3>
                <form method="GET" action="{{ route('products.index') }}">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un produit..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                    <button type="submit" class="w-full mt-3 bg-vert-energie text-white py-2 rounded-lg hover:bg-green-700 transition">
                        Rechercher
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des produits -->
        <div class="lg:w-3/4">
            <!-- Barre de tri -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                <p class="text-gray-600 mb-4 sm:mb-0">
                    {{ $products->total() }} produit(s) trouvé(s)
                </p>

                <form method="GET" action="{{ route('products.index') }}" class="flex items-center">
                    <input type="hidden" name="category" value="{{ request('category') }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <label class="mr-2 text-gray-600">Trier par:</label>
                    <select name="sort" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom A-Z</option>
                    </select>
                </form>
            </div>

            <!-- Grille des produits -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
                    <!-- Section image/vidéo corrigée -->
                    <div class="relative">
                        @if($product->videos && count($product->videos) > 0)
                            <!-- Si le produit a des vidéos, afficher la première vidéo -->
                            <video
                                class="w-full h-64 object-cover group-hover:scale-105 transition"
                                muted
                                loop
                                preload="metadata"
                                onmouseover="this.play()"
                                onmouseout="this.pause(); this.currentTime = 0;"
                                poster="{{ $product->first_image }}"
                            >
                                <source src="{{ asset($product->videos[0]) }}" type="video/mp4">
                                <!-- Fallback vers l'image si la vidéo ne charge pas -->
                                <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-64 object-cover">
                            </video>

                            <!-- Indicateur vidéo -->
                            <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded-full text-xs flex items-center">
                                🎥 Vidéo
                            </div>
                        @else
                            <!-- Si pas de vidéo, afficher l'image normale -->
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-64 object-cover group-hover:scale-105 transition">
                        @endif

                        @if($product->promotional_price)
                        <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            -{{ round((($product->price - $product->promotional_price) / $product->price) * 100) }}%
                        </div>
                        @endif

                        @if($product->stock_quantity < 10 && $product->stock_quantity > 0)
                        <div class="absolute bottom-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            Stock limité
                        </div>
                        @elseif($product->stock_quantity == 0)
                        <div class="absolute bottom-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            Rupture
                        </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="text-sm text-vert-energie font-medium mb-2">{{ $product->category->name }}</div>
                        <h3 class="font-montserrat font-semibold text-lg mb-3 text-gris-moderne">{{ $product->name }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit($product->description, 100) }}</p>

                        <!-- Prix -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                @if($product->promotional_price)
                                <span class="text-2xl font-bold text-red-500">{{ number_format($product->promotional_price, 0, ',', ' ') }} FCFA</span>
                                <span class="text-lg text-gray-500 line-through">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                @else
                                <span class="text-2xl font-bold text-vert-energie">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </div>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="flex items-center justify-between mb-4 text-sm text-gray-600">
                            <span>Stock: {{ $product->stock_quantity }}</span>
                            @if($product->warranty)
                            <span>Garantie: {{ $product->warranty }}</span>
                            @endif
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex space-x-2">
                            <a href="{{ route('products.show', $product->slug) }}" class="flex-1 bg-vert-energie text-white text-center py-2 rounded-lg hover:bg-green-700 transition font-medium">
                                @if($product->videos && count($product->videos) > 0)
                                🎥 Voir vidéo
                                @else
                                👁️ Voir détails
                                @endif
                            </a>
                            <a href="https://wa.me/22665033700?text=Bonjour, je suis intéressé par {{ $product->name }} - Prix: {{ number_format($product->current_price, 0, ',', ' ') }} FCFA" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                                💬
                            </a>
                            <a href="{{ route('orders.create', ['product_id' => $product->id]) }}" class="bg-orange-burkina text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                                🛒
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-montserrat font-semibold text-gris-moderne mb-2">Aucun produit trouvé</h3>
                    <p class="text-gray-600 mb-4">Essayez de modifier vos critères de recherche</p>
                    <a href="{{ route('products.index') }}" class="bg-vert-energie text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        Voir tous les produits
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="mt-8">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
/* Amélioration de l'affichage des vidéos */
video {
    transition: all 0.3s ease;
}

video:hover {
    transform: scale(1.02);
}

/* Style pour les cartes produits */
.group:hover video {
    filter: brightness(1.1);
}

/* Responsive pour les vidéos */
@media (max-width: 768px) {
    video {
        height: 200px;
    }
}
</style>
@endpush
@endsection

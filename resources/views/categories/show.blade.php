@extends('layouts.public')

@section('title', $category->name . ' - Grossiste Ouaga International')
@section('description', $category->description)

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <nav class="text-sm text-gray-600 mb-4">
            <a href="{{ route('home') }}" class="hover:text-vert-energie">Accueil</a>
            <span class="mx-2">/</span>
            <a href="{{ route('categories.index') }}" class="hover:text-vert-energie">Catégories</a>
            <span class="mx-2">/</span>
            <span>{{ $category->name }}</span>
        </nav>
        
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl md:text-4xl font-montserrat font-bold text-gris-moderne mb-4">
                    {{ $category->name }}
                </h1>
                <p class="text-gray-600 text-lg mb-4">{{ $category->description }}</p>
                <p class="text-vert-energie font-medium">{{ $products->total() }} produit(s) disponible(s)</p>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    @if($products->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
            <div class="relative">
                <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-64 object-cover group-hover:scale-105 transition">
                @if($product->promotional_price)
                <div class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    -{{ round((($product->price - $product->promotional_price) / $product->price) * 100) }}%
                </div>
                @endif
                @if($product->stock_quantity < 10)
                <div class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                    Stock limité
                </div>
                @endif
            </div>
            <div class="p-6">
                <h3 class="font-montserrat font-semibold text-lg mb-3 text-gris-moderne">{{ $product->name }}</h3>
                <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit($product->description, 100) }}</p>
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
                <div class="flex space-x-2">
                    <a href="{{ route('products.show', $product->slug) }}" class="flex-1 bg-vert-energie text-white text-center py-2 rounded-lg hover:bg-green-700 transition">
                        Voir détails
                    </a>
                    <a href="https://wa.me/22670123456?text=Je suis intéressé par {{ $product->name }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        💬
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-12">
        {{ $products->links() }}
    </div>
    @endif
    @else
    <div class="text-center py-12">
        <div class="text-6xl mb-4">📦</div>
        <h3 class="text-xl font-montserrat font-semibold text-gris-moderne mb-2">Aucun produit dans cette catégorie</h3>
        <p class="text-gray-600 mb-4">Cette catégorie sera bientôt remplie de produits fantastiques !</p>
        <a href="{{ route('categories.index') }}" class="bg-vert-energie text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
            Voir toutes les catégories
        </a>
    </div>
    @endif
</div>
@endsection

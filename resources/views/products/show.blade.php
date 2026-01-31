@extends('layouts.public')

@section('title', $product->meta_title ?: $product->name . ' - Grossiste Ouaga International')
@section('description', $product->meta_description ?: Str::limit($product->description, 160))
@section('keywords', $product->category->name . ', ' . $product->name . ', énergie solaire, Burkina Faso')
@section('og_title', $product->name . ' - ' . number_format($product->current_price, 0, ',', ' ') . ' FCFA')
@section('og_description', Str::limit($product->description, 160))
@section('og_image', $product->first_image)

@push('structured_data')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "{{ $product->name }}",
  "image": "{{ $product->first_image }}",
  "description": "{{ Str::limit($product->description, 160) }}",
  "brand": {
    "@type": "Brand",
    "name": "Grossiste Ouaga International"
  },
  "offers": {
    "@type": "Offer",
    "url": "{{ url()->current() }}",
    "priceCurrency": "XOF",
    "price": "{{ $product->current_price }}",
    "priceValidUntil": "{{ now()->addYear()->format('Y-m-d') }}",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "{{ $product->stock_quantity > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' }}"
  }
}
</script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-vert-energie">Accueil</a></li>
            <li><span>/</span></li>
            <li><a href="{{ route('products.index') }}" class="hover:text-vert-energie">Produits</a></li>
            <li><span>/</span></li>
            <li><a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-vert-energie">{{ $product->category->name }}</a></li>
            <li><span>/</span></li>
            <li class="text-gray-900 font-medium">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Section Images --}}
        <div class="space-y-4">
            {{-- Image principale --}}
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden shadow-lg">
                @if($product->images && count($product->images) > 0)
                    <img src="{{ $product->first_image }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover"
                         id="main-image">
                @else
                    <img src="{{ asset('images/placeholder-product.jpg') }}"
                         alt="Image non disponible"
                         class="w-full h-full object-cover">
                @endif
            </div>

            {{-- Galerie de miniatures --}}
            @if($product->image_urls && count($product->image_urls) > 1)
            <div class="flex space-x-2 overflow-x-auto pb-2">
                @foreach($product->image_urls as $index => $imageUrl)
                    <img src="{{ $imageUrl }}"
                         alt="{{ $product->name }} - Image {{ $index + 1 }}"
                         class="w-20 h-20 object-cover rounded cursor-pointer border-2 border-transparent hover:border-vert-energie transition-all duration-200 flex-shrink-0"
                         onclick="document.getElementById('main-image').src = '{{ $imageUrl }}'">
                @endforeach
            </div>
            @endif

            {{-- Vidéos --}}
            @if($product->video_urls && count($product->video_urls) > 0)
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900">Vidéos du produit</h3>
                @foreach($product->video_urls as $videoUrl)
                    <video controls class="w-full rounded-lg shadow-md">
                        <source src="{{ $videoUrl }}" type="video/mp4">
                        Votre navigateur ne supporte pas les vidéos.
                    </video>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Section Informations --}}
        <div class="space-y-6">
            {{-- Titre et catégorie --}}
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="bg-vert-energie text-white text-xs px-2 py-1 rounded">{{ $product->category->name }}</span>
                    @if($product->is_featured)
                        <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">⭐ Vedette</span>
                    @endif
                </div>
                <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ $product->name }}</h1>
            </div>

            {{-- Prix --}}
            <div class="border-b border-gray-200 pb-4">
                <div class="text-3xl font-bold text-vert-energie">
                    @if($product->promotional_price)
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-400 line-through text-xl">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                            <span>{{ number_format($product->promotional_price, 0, ',', ' ') }} FCFA</span>
                            <span class="bg-red-500 text-white text-sm px-2 py-1 rounded">
                                -{{ round((($product->price - $product->promotional_price) / $product->price) * 100) }}%
                            </span>
                        </div>
                    @else
                        {{ number_format($product->price, 0, ',', ' ') }} FCFA
                    @endif
                </div>

                {{-- Stock --}}
                <div class="mt-2">
                    @if($product->stock_quantity > 0)
                        <span class="text-green-600 text-sm font-medium">✅ En stock ({{ $product->stock_quantity }} unités)</span>
                    @else
                        <span class="text-red-600 text-sm font-medium">❌ Rupture de stock</span>
                    @endif
                </div>
            </div>

            {{-- Description --}}
            <div>
                <h2 class="text-xl font-semibold mb-3 text-gray-900">Description</h2>
                <div class="text-gray-700 leading-relaxed">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

            {{-- Spécifications techniques --}}
            @if($product->specifications)
            <div>
                <h2 class="text-xl font-semibold mb-3 text-gray-900">Spécifications techniques</h2>
                <div class="text-gray-700 leading-relaxed">
                    {!! nl2br(e($product->specifications)) !!}
                </div>
            </div>
            @endif

            {{-- Garantie --}}
            @if($product->warranty)
            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="font-semibold text-blue-900 mb-2">🛡️ Garantie</h3>
                <p class="text-blue-800">{{ $product->warranty }}</p>
            </div>
            @endif

            {{-- Boutons d'action --}}
            <div class="space-y-3 border-t border-gray-200 pt-6">
                <a href="{{ route('orders.create', ['product_id' => $product->id]) }}"
                   class="w-full bg-vert-energie text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-200 text-center block">
                    🛒 Commander ce produit
                </a>

                <div class="grid grid-cols-2 gap-3">
                    <a href="tel:+22665033700"
                       class="bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700 transition duration-200 text-center">
                        📞 Appeler
                    </a>
                    <a href="https://wa.me/22665033700?text=Bonjour, je suis intéressé par {{ urlencode($product->name) }}"
                       target="_blank"
                       class="bg-green-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-green-700 transition duration-200 text-center">
                        💬 WhatsApp
                    </a>
                </div>
            </div>

            {{-- Informations supplémentaires --}}
            <div class="bg-gray-50 p-4 rounded-lg text-sm text-gray-600">
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="mr-2">🚚</span>
                        <span>Livraison disponible à Ouagadougou et environs</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">💳</span>
                        <span>Paiement : Espèces, Orange Money, Moov Money, Virement</span>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">⚡</span>
                        <span>Installation et maintenance disponibles</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Produits similaires --}}
    @if($relatedProducts && $relatedProducts->count() > 0)
    <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Produits similaires</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-200">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}">
                        <div class="aspect-square overflow-hidden">
                            <img src="{{ $relatedProduct->first_image }}"
                                 alt="{{ $relatedProduct->name }}"
                                 class="w-full h-full object-cover hover:scale-105 transition duration-200">
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                            <div class="text-vert-energie font-bold">
                                @if($relatedProduct->promotional_price)
                                    <span class="text-gray-400 line-through text-sm">{{ number_format($relatedProduct->price, 0, ',', ' ') }} FCFA</span><br>
                                    <span>{{ number_format($relatedProduct->promotional_price, 0, ',', ' ') }} FCFA</span>
                                @else
                                    {{ number_format($relatedProduct->price, 0, ',', ' ') }} FCFA
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- DEBUG TEMPORAIRE (uniquement en mode développement) --}}
    @if(config('app.debug'))
    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <h3 class="font-bold text-yellow-800 mb-2">🔧 Debug Info (Mode développement):</h3>
        <div class="text-sm text-yellow-700 space-y-1">
            <p><strong>Images (brut):</strong> {{ json_encode($product->images) }}</p>
            <p><strong>Images URLs:</strong> {{ json_encode($product->image_urls ?? []) }}</p>
            <p><strong>Première image:</strong> {{ $product->first_image }}</p>
            <p><strong>Vidéos (brut):</strong> {{ json_encode($product->videos) }}</p>
            <p><strong>Vidéos URLs:</strong> {{ json_encode($product->video_urls ?? []) }}</p>
            <p><strong>Stock:</strong> {{ $product->stock_quantity }}</p>
            <p><strong>Prix actuel:</strong> {{ $product->current_price }}</p>
        </div>
    </div>
    @endif
</div>

{{-- Scripts pour la galerie d'images --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Galerie d'images avec navigation au clavier
    const mainImage = document.getElementById('main-image');
    const thumbnails = document.querySelectorAll('[onclick*="main-image"]');

    if (thumbnails.length > 0) {
        let currentIndex = 0;

        // Navigation au clavier
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && currentIndex > 0) {
                currentIndex--;
                thumbnails[currentIndex].click();
            } else if (e.key === 'ArrowRight' && currentIndex < thumbnails.length - 1) {
                currentIndex++;
                thumbnails[currentIndex].click();
            }
        });

        // Mise à jour de l'index actuel
        thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', function() {
                currentIndex = index;
                // Ajouter une bordure à la miniature active
                thumbnails.forEach(t => t.classList.remove('border-vert-energie'));
                this.classList.add('border-vert-energie');
            });
        });

        // Marquer la première miniature comme active
        if (thumbnails[0]) {
            thumbnails[0].classList.add('border-vert-energie');
        }
    }
});
</script>
@endpush
@endsection

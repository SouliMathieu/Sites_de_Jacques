@extends('layouts.public')

@section('title', $product->meta_title ?: $product->name . ' - Jackson Energy International')
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
  "image": [
    @if($product->hasImages())
        @foreach($product->image_urls as $index => $imageUrl)
            "{{ $imageUrl }}"{{ $loop->last ? '' : ',' }}
        @endforeach
    @else
        "{{ asset('images/placeholder-product.jpg') }}"
    @endif
  ],
  "description": "{{ Str::limit($product->description, 160) }}",
  "brand": {
    "@type": "Brand",
    "name": "Jackson Energy International"
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
        {{-- ✅ Section Images et Vidéos --}}
        <div class="space-y-4">
            {{-- ✅ Image/Vidéo principale --}}
            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden shadow-lg">
                @if($product->hasImages())
                    <img src="{{ $product->first_image }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover cursor-pointer"
                         id="main-image"
                         onclick="openLightbox(0)">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-200">
                        <div class="text-center text-gray-500">
                            <span class="text-6xl mb-4 block">📷</span>
                            <span>Aucune image disponible</span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ✅ Compteurs de médias --}}
            @if($product->hasImages() || $product->hasVideos())
            <div class="flex items-center justify-center space-x-4 text-sm text-gray-600">
                @if($product->hasImages())
                    <span class="flex items-center space-x-1">
                        <span>📸</span>
                        <span>{{ $product->images_count }} image{{ $product->images_count > 1 ? 's' : '' }}</span>
                    </span>
                @endif
                @if($product->hasVideos())
                    <span class="flex items-center space-x-1">
                        <span>🎥</span>
                        <span>{{ $product->videos_count }} vidéo{{ $product->videos_count > 1 ? 's' : '' }}</span>
                    </span>
                @endif
            </div>
            @endif

            {{-- ✅ Galerie de miniatures --}}
            @if($product->hasImages() && $product->images_count > 1)
            <div class="flex space-x-2 overflow-x-auto pb-2">
                @foreach($product->image_urls as $index => $imageUrl)
                    <img src="{{ $imageUrl }}"
                         alt="{{ $product->name }} - Image {{ $index + 1 }}"
                         class="w-20 h-20 object-cover rounded cursor-pointer border-2 border-transparent hover:border-vert-energie transition-all duration-200 flex-shrink-0 {{ $index === 0 ? 'border-vert-energie' : '' }}"
                         onclick="changeMainImage('{{ $imageUrl }}', {{ $index }})">
                @endforeach
            </div>
            @endif

            {{-- ✅ Vidéos --}}
            @if($product->hasVideos())
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="mr-2">🎥</span>
                    Vidéo{{ $product->videos_count > 1 ? 's' : '' }} du produit
                </h3>
                <div class="space-y-3">
                    @foreach($product->video_urls as $index => $videoUrl)
                        <div class="relative">
                            <video controls class="w-full rounded-lg shadow-md max-h-64" preload="metadata">
                                <source src="{{ $videoUrl }}" type="video/mp4">
                                <source src="{{ $videoUrl }}" type="video/webm">
                                <source src="{{ $videoUrl }}" type="video/ogg">
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                            @if($product->videos_count > 1)
                                <div class="absolute top-2 left-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded text-xs">
                                    Vidéo {{ $index + 1 }}/{{ $product->videos_count }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
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
                    @if($product->hasImages())
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">📸 {{ $product->images_count }}</span>
                    @endif
                    @if($product->hasVideos())
                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">🎥 {{ $product->videos_count }}</span>
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
                @if($product->stock_quantity > 0)
                    <a href="{{ route('orders.create', ['product_id' => $product->id]) }}"
                       class="w-full bg-vert-energie text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-200 text-center block">
                        🛒 Commander ce produit
                    </a>
                @else
                    <div class="w-full bg-gray-400 text-white py-3 px-6 rounded-lg font-semibold text-center">
                        ❌ Produit en rupture de stock
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <a href="tel:+22677126519"
                       class="bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700 transition duration-200 text-center">
                        📞 Appeler
                    </a>
                    <a href="https://wa.me/22677126519?text=Bonjour, je suis intéressé par {{ urlencode($product->name) }} - Prix: {{ number_format($product->current_price, 0, ',', ' ') }} FCFA"
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
                        <div class="aspect-square overflow-hidden relative">
                            @if($relatedProduct->hasImages())
                                <img src="{{ $relatedProduct->first_image }}"
                                     alt="{{ $relatedProduct->name }}"
                                     class="w-full h-full object-cover hover:scale-105 transition duration-200">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500 text-4xl">📷</span>
                                </div>
                            @endif
                            @if($relatedProduct->hasImages() || $relatedProduct->hasVideos())
                                <div class="absolute top-2 right-2 flex space-x-1">
                                    @if($relatedProduct->hasImages())
                                        <span class="bg-black bg-opacity-75 text-white px-1 py-0.5 rounded text-xs">📸{{ $relatedProduct->images_count }}</span>
                                    @endif
                                    @if($relatedProduct->hasVideos())
                                        <span class="bg-black bg-opacity-75 text-white px-1 py-0.5 rounded text-xs">🎥{{ $relatedProduct->videos_count }}</span>
                                    @endif
                                </div>
                            @endif
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

    {{-- ✅ Lightbox pour galerie d'images --}}
    @if($product->hasImages())
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-3xl font-bold hover:text-gray-300 z-10">
                ×
            </button>
            <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain">
            @if($product->images_count > 1)
                <button onclick="previousImage()" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white text-3xl font-bold hover:text-gray-300">
                    ‹
                </button>
                <button onclick="nextImage()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white text-3xl font-bold hover:text-gray-300">
                    ›
                </button>
            @endif
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-sm">
                <span id="image-counter">1</span> / {{ $product->images_count }}
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ✅ Scripts pour la galerie d'images --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ Données des images
    const images = @json($product->image_urls ?? []);
    let currentImageIndex = 0;

    // ✅ Navigation dans la galerie
    window.changeMainImage = function(imageUrl, index) {
        document.getElementById('main-image').src = imageUrl;
        currentImageIndex = index;
        
        // Mettre à jour les bordures des miniatures
        document.querySelectorAll('[onclick*="changeMainImage"]').forEach((thumb, i) => {
            thumb.classList.toggle('border-vert-energie', i === index);
        });
    };

    // ✅ Lightbox
    window.openLightbox = function(index) {
        if (images.length === 0) return;
        
        currentImageIndex = index;
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const counter = document.getElementById('image-counter');
        
        lightboxImage.src = images[currentImageIndex];
        counter.textContent = currentImageIndex + 1;
        lightbox.classList.remove('hidden');
        
        // Empêcher le scroll du body
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function() {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = 'auto';
    };

    window.previousImage = function() {
        if (currentImageIndex > 0) {
            currentImageIndex--;
        } else {
            currentImageIndex = images.length - 1;
        }
        updateLightboxImage();
    };

    window.nextImage = function() {
        if (currentImageIndex < images.length - 1) {
            currentImageIndex++;
        } else {
            currentImageIndex = 0;
        }
        updateLightboxImage();
    };

    function updateLightboxImage() {
        const lightboxImage = document.getElementById('lightbox-image');
        const counter = document.getElementById('image-counter');
        
        lightboxImage.src = images[currentImageIndex];
        counter.textContent = currentImageIndex + 1;
    }

    // ✅ Navigation au clavier
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (!lightbox.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                previousImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            }
        }
    });

    // Fermer lightbox en cliquant en dehors de l'image
    document.getElementById('lightbox')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
});
</script>
@endpush
@endsection

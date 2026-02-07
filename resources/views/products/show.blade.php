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
  "image": {{ json_encode($product->images_count > 0 ? $product->image_urls : [asset('images/placeholder-product.jpg')]) }},
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
<div class="bg-gray-50 border-b">
    <div class="container mx-auto px-4 py-4">
        <nav aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-green-600 transition">🏠 Accueil</a></li>
                <li><span class="text-gray-400">›</span></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-green-600 transition">Produits</a></li>
                <li><span class="text-gray-400">›</span></li>
                <li><a href="{{ route('categories.show', $product->category->slug) }}" class="hover:text-green-600 transition">{{ $product->category->name }}</a></li>
                <li><span class="text-gray-400">›</span></li>
                <li class="text-gray-900 font-semibold truncate max-w-xs">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mx-auto px-4 py-8 lg:py-12">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
        <div class="space-y-4">
            <div class="relative aspect-square bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl overflow-hidden shadow-2xl group">
                @if($product->images_count > 0)
                    <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-full h-full object-cover cursor-zoom-in transition-transform duration-500 group-hover:scale-110" id="main-image" onclick="openLightbox(0)">
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                        <span class="bg-white/90 backdrop-blur-sm text-gray-900 px-4 py-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300 font-semibold text-sm">🔍 Cliquez pour agrandir</span>
                    </div>
                    <div class="absolute top-4 left-4 flex flex-col gap-2">
                        @if($product->is_featured)
                            <span class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white text-xs px-3 py-1.5 rounded-full shadow-lg font-bold">⭐ Vedette</span>
                        @endif
                        @if($product->promotional_price)
                            <span class="bg-gradient-to-r from-red-500 to-red-600 text-white text-xs px-3 py-1.5 rounded-full shadow-lg font-bold">-{{ round((($product->price - $product->promotional_price) / $product->price) * 100) }}% OFF</span>
                        @endif
                    </div>
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center">
                        <span class="text-8xl mb-4 opacity-20">📷</span>
                        <span class="text-gray-400 font-medium">Aucune image disponible</span>
                    </div>
                @endif
            </div>

            @if($product->images_count > 0 || $product->videos_count > 0)
            <div class="flex items-center justify-center gap-4">
                @if($product->images_count > 0)
                    <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-full">
                        <span class="text-lg">📸</span>
                        <span class="font-semibold">{{ $product->images_count }} image{{ $product->images_count > 1 ? 's' : '' }}</span>
                    </div>
                @endif
                @if($product->videos_count > 0)
                    <div class="flex items-center gap-2 bg-purple-50 text-purple-700 px-4 py-2 rounded-full">
                        <span class="text-lg">🎥</span>
                        <span class="font-semibold">{{ $product->videos_count }} vidéo{{ $product->videos_count > 1 ? 's' : '' }}</span>
                    </div>
                @endif
            </div>
            @endif

            @if($product->images_count > 1)
            <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                @foreach($product->image_urls as $index => $imageUrl)
                    <div class="relative flex-shrink-0 group">
                        <img src="{{ $imageUrl }}" alt="{{ $product->name }} - Image {{ $index + 1 }}" class="w-20 h-20 lg:w-24 lg:h-24 object-cover rounded-xl cursor-pointer border-3 transition-all duration-300 {{ $index === 0 ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200 hover:border-green-400' }}" onclick="changeMainImage('{{ $imageUrl }}', {{ $index }})">
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-20 transition-opacity rounded-xl"></div>
                    </div>
                @endforeach
            </div>
            @endif

            @if($product->videos_count > 0)
            <div class="space-y-4 pt-4 border-t">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <span class="bg-purple-100 text-purple-600 rounded-full w-8 h-8 flex items-center justify-center mr-3">🎥</span>
                    Vidéo{{ $product->videos_count > 1 ? 's' : '' }} du produit
                </h3>
                <div class="space-y-4">
                    @foreach($product->video_urls as $index => $videoUrl)
                        <div class="relative rounded-xl overflow-hidden shadow-lg">
                            <video controls class="w-full rounded-xl max-h-80 bg-black" preload="metadata" controlsList="nodownload" poster="{{ $product->first_image ?? '' }}">
                                <source src="{{ $videoUrl }}" type="video/mp4">
                                <source src="{{ $videoUrl }}" type="video/webm">
                                <source src="{{ $videoUrl }}" type="video/ogg">
                                Votre navigateur ne supporte pas la lecture de vidéos.
                            </video>
                            @if($product->videos_count > 1)
                                <div class="absolute top-3 left-3 bg-black/75 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-xs font-bold">
                                    Vidéo {{ $index + 1 }}/{{ $product->videos_count }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="bg-gradient-to-r from-green-500 to-green-600 text-white text-sm px-3 py-1 rounded-full font-semibold shadow-md">{{ $product->category->name }}</span>
                    @if($product->images_count > 0)
                        <span class="bg-blue-100 text-blue-700 text-sm px-3 py-1 rounded-full font-semibold">📸 {{ $product->images_count }}</span>
                    @endif
                    @if($product->videos_count > 0)
                        <span class="bg-purple-100 text-purple-700 text-sm px-3 py-1 rounded-full font-semibold">🎥 {{ $product->videos_count }}</span>
                    @endif
                </div>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 leading-tight mb-4">{{ $product->name }}</h1>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-2xl p-6 border-2 border-green-200">
                <div class="flex items-baseline gap-3 mb-4">
                    @if($product->promotional_price)
                        <span class="text-gray-400 line-through text-xl lg:text-2xl font-semibold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                        <span class="text-4xl lg:text-5xl font-bold text-green-600">{{ number_format($product->promotional_price, 0, ',', ' ') }} FCFA</span>
                    @else
                        <span class="text-4xl lg:text-5xl font-bold text-green-600">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if($product->stock_quantity > 0)
                        <span class="inline-flex items-center gap-2 bg-green-100 text-green-800 text-sm font-bold px-4 py-2 rounded-full">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            En stock ({{ $product->stock_quantity }} unités disponibles)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 bg-red-100 text-red-800 text-sm font-bold px-4 py-2 rounded-full">
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            Rupture de stock
                        </span>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border-2 border-gray-100 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-lg">📝</span>
                    Description
                </h2>
                <div class="text-gray-700 leading-relaxed space-y-3">{!! nl2br(e($product->description)) !!}</div>
            </div>

            @if($product->specifications)
            <div class="bg-white rounded-xl p-6 border-2 border-gray-100 shadow-sm">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <span class="bg-purple-100 text-purple-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-lg">⚙️</span>
                    Spécifications techniques
                </h2>
                <div class="text-gray-700 leading-relaxed space-y-2">{!! nl2br(e($product->specifications)) !!}</div>
            </div>
            @endif

            @if($product->warranty)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
                <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center"><span class="text-2xl mr-2">🛡️</span> Garantie</h3>
                <p class="text-blue-800 font-medium">{{ $product->warranty }}</p>
            </div>
            @endif

            <div class="space-y-4 pt-6 border-t-2">
                @if($product->stock_quantity > 0)
                    <a href="{{ route('orders.create', ['product_id' => $product->id]) }}" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-4 px-8 rounded-xl font-bold text-lg shadow-lg hover:shadow-2xl transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        <span class="text-2xl">🛒</span><span>Commander ce produit</span>
                    </a>
                @else
                    <div class="w-full bg-gray-300 text-gray-600 py-4 px-8 rounded-xl font-bold text-lg text-center flex items-center justify-center gap-2 cursor-not-allowed">
                        <span class="text-2xl">❌</span><span>Produit en rupture de stock</span>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <a href="tel:+22665033700" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-xl font-bold shadow-md hover:shadow-xl transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        <span class="text-xl">📞</span><span>Appeler</span>
                    </a>
                    <a href="https://wa.me/22665033700?text=Bonjour, je suis intéressé par {{ urlencode($product->name) }} - Prix: {{ number_format($product->current_price, 0, ',', ' ') }} FCFA" target="_blank" class="bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-xl font-bold shadow-md hover:shadow-xl transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        <span class="text-xl">💬</span><span>WhatsApp</span>
                    </a>
                </div>
            </div>

            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-gray-200">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <span class="text-xl mr-2">ℹ️</span> Informations utiles
                </h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="text-green-600 text-2xl flex-shrink-0">🚚</span>
                        <div>
                            <p class="font-semibold text-gray-900">Livraison disponible</p>
                            <p class="text-sm text-gray-600">À Ouagadougou et dans tout le Burkina Faso</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-blue-600 text-2xl flex-shrink-0">💳</span>
                        <div>
                            <p class="font-semibold text-gray-900">Paiements acceptés</p>
                            <p class="text-sm text-gray-600">Espèces, Orange Money, Moov Money, Virement bancaire</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-purple-600 text-2xl flex-shrink-0">⚡</span>
                        <div>
                            <p class="font-semibold text-gray-900">Services inclus</p>
                            <p class="text-sm text-gray-600">Installation professionnelle et maintenance disponibles</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="text-orange-600 text-2xl flex-shrink-0">🎯</span>
                        <div>
                            <p class="font-semibold text-gray-900">Support technique</p>
                            <p class="text-sm text-gray-600">Assistance et conseil personnalisé gratuits</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts && $relatedProducts->count() > 0)
    <div class="mt-16 lg:mt-24">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Produits similaires</h2>
            <p class="text-gray-600">Découvrez d'autres produits qui pourraient vous intéresser</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
                <div class="group bg-white rounded-2xl shadow-md hover:shadow-2xl overflow-hidden transition-all duration-300 transform hover:-translate-y-2">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}">
                        <div class="relative aspect-square overflow-hidden bg-gray-100">
                            @if($relatedProduct->images_count > 0)
                                <img src="{{ $relatedProduct->first_image }}" alt="{{ $relatedProduct->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center"><span class="text-gray-300 text-6xl">📷</span></div>
                            @endif
                            @if($relatedProduct->images_count > 0 || $relatedProduct->videos_count > 0)
                                <div class="absolute top-3 right-3 flex gap-1">
                                    @if($relatedProduct->images_count > 0)
                                        <span class="bg-black/75 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs font-bold">📸{{ $relatedProduct->images_count }}</span>
                                    @endif
                                    @if($relatedProduct->videos_count > 0)
                                        <span class="bg-black/75 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs font-bold">🎥{{ $relatedProduct->videos_count }}</span>
                                    @endif
                                </div>
                            @endif
                            @if($relatedProduct->promotional_price)
                                <div class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                    -{{ round((($relatedProduct->price - $relatedProduct->promotional_price) / $relatedProduct->price) * 100) }}%
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-green-600 transition-colors">{{ $relatedProduct->name }}</h3>
                            <div class="flex flex-col gap-1">
                                @if($relatedProduct->promotional_price)
                                    <span class="text-gray-400 line-through text-sm">{{ number_format($relatedProduct->price, 0, ',', ' ') }} FCFA</span>
                                    <span class="text-green-600 font-bold text-lg">{{ number_format($relatedProduct->promotional_price, 0, ',', ' ') }} FCFA</span>
                                @else
                                    <span class="text-green-600 font-bold text-lg">{{ number_format($relatedProduct->price, 0, ',', ' ') }} FCFA</span>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($product->images_count > 0)
    <div id="lightbox" class="fixed inset-0 bg-black/95 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="relative max-w-6xl max-h-full w-full h-full flex items-center justify-center">
            <button onclick="closeLightbox()" class="absolute top-4 right-4 bg-white/10 hover:bg-white/20 text-white w-12 h-12 rounded-full flex items-center justify-center text-3xl font-bold transition-all z-10 backdrop-blur-sm">×</button>
            <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
            @if($product->images_count > 1)
                <button onclick="previousImage()" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white w-12 h-12 rounded-full flex items-center justify-center text-3xl font-bold transition-all backdrop-blur-sm">‹</button>
                <button onclick="nextImage()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/10 hover:bg-white/20 text-white w-12 h-12 rounded-full flex items-center justify-center text-3xl font-bold transition-all backdrop-blur-sm">›</button>
            @endif
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 bg-white/10 backdrop-blur-sm text-white px-4 py-2 rounded-full font-semibold">
                <span id="image-counter">1</span> / {{ $product->images_count }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const images = @json($product->image_urls ?? []);
    let currentImageIndex = 0;

    window.changeMainImage = function(imageUrl, index) {
        const mainImage = document.getElementById('main-image');
        if (mainImage) {
            mainImage.src = imageUrl;
            currentImageIndex = index;
            document.querySelectorAll('[onclick*="changeMainImage"]').forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('border-green-500', 'ring-2', 'ring-green-200');
                    thumb.classList.remove('border-gray-200');
                } else {
                    thumb.classList.remove('border-green-500', 'ring-2', 'ring-green-200');
                    thumb.classList.add('border-gray-200');
                }
            });
        }
    };

    window.openLightbox = function(index) {
        if (images.length === 0) return;
        currentImageIndex = index;
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const counter = document.getElementById('image-counter');
        if (lightbox && lightboxImage && counter) {
            lightboxImage.src = images[currentImageIndex];
            counter.textContent = currentImageIndex + 1;
            lightbox.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeLightbox = function() {
        const lightbox = document.getElementById('lightbox');
        if (lightbox) {
            lightbox.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    };

    window.previousImage = function() {
        currentImageIndex = currentImageIndex > 0 ? currentImageIndex - 1 : images.length - 1;
        updateLightboxImage();
    };

    window.nextImage = function() {
        currentImageIndex = currentImageIndex < images.length - 1 ? currentImageIndex + 1 : 0;
        updateLightboxImage();
    };

    function updateLightboxImage() {
        const lightboxImage = document.getElementById('lightbox-image');
        const counter = document.getElementById('image-counter');
        if (lightboxImage && counter) {
            lightboxImage.src = images[currentImageIndex];
            counter.textContent = currentImageIndex + 1;
        }
    }

    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox && !lightbox.classList.contains('hidden')) {
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') previousImage();
            else if (e.key === 'ArrowRight') nextImage();
        }
    });

    const lightbox = document.getElementById('lightbox');
    if (lightbox) {
        lightbox.addEventListener('click', function(e) {
            if (e.target === this) closeLightbox();
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.scrollbar-hide::-webkit-scrollbar { display: none; }
.scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endpush

@endsection
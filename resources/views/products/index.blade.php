@extends('layouts.app', [
    'title' => 'Nos Produits - Jackson Energy International',
    'description' => 'Découvrez nos produits solaires : panneaux, batteries, onduleurs, régulateurs au Burkina Faso.'
])

@section('content')
    {{-- Header de la page --}}
    <section class="products-header">
        <div class="products-header__container">
            <nav class="products-breadcrumb">
                <a href="{{ route('home') }}" class="products-breadcrumb__link">Accueil</a>
                <span class="products-breadcrumb__separator">/</span>
                <span class="products-breadcrumb__current">Produits</span>
            </nav>
            <h1 class="products-header__title">Nos Produits</h1>
            <p class="products-header__subtitle">
                Solutions solaires complètes pour l'autonomie énergétique
            </p>
        </div>
    </section>

    {{-- Section produits --}}
    <section class="products-section">
        <div class="products-section__container">
            <div class="products-layout">
                {{-- Sidebar filtres --}}
                <aside class="products-filters">
                    <div class="products-filters__card">
                        <h2 class="products-filters__title">Catégories</h2>

                        <div class="products-filters__categories">
                            <a href="{{ route('products.index') }}"
                               class="products-filters__category-link {{ !request('category') ? 'products-filters__category-link--active' : '' }}">
                                <div class="products-filters__category-inner">
                                    <span>Tous les produits</span>
                                    @if(isset($totalProducts))
                                        <span class="products-filters__badge products-filters__badge--green">
                                            {{ $totalProducts }}
                                        </span>
                                    @endif
                                </div>
                            </a>

                            @foreach($categories ?? [] as $cat)
                                <a href="{{ route('products.index', ['category' => $cat->id]) }}"
                                   class="products-filters__category-link {{ request('category') == $cat->id ? 'products-filters__category-link--active' : '' }}">
                                    <div class="products-filters__category-inner">
                                        <span>{{ $cat->name }}</span>
                                        <span class="products-filters__badge">
                                            {{ $cat->products_count ?? 0 }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <hr class="products-filters__divider">

                        <h3 class="products-filters__subtitle">Recherche</h3>
                        <form action="{{ route('products.index') }}" method="GET" class="products-filters__form">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Rechercher un produit..."
                                class="products-filters__input"
                            >
                            <button type="submit" class="products-filters__button">
                                Rechercher
                            </button>
                        </form>
                    </div>
                </aside>

                {{-- Liste des produits --}}
                <div class="products-list">
                    <div class="products-list__header">
                        <p class="products-list__count">
                            <span class="products-list__count-number">{{ $products->total() ?? 0 }}</span>
                            <span class="products-list__count-label">produit(s) trouvé(s)</span>
                        </p>

                        <form action="{{ route('products.index') }}" method="GET" class="products-list__sort-form">
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="category" value="{{ request('category') }}">

                            <label for="sort" class="products-list__sort-label">Trier par:</label>
                            <select
                                name="sort"
                                id="sort"
                                onchange="this.form.submit()"
                                class="products-list__sort-select"
                            >
                                <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nom A-Z</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nom Z-A</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            </select>
                        </form>
                    </div>

                    @if($products->count() > 0)
                        <div class="products-grid">
                            @foreach($products as $product)
                                <div class="product-card">
                                    {{-- Image ou Vidéo avec lecture au survol --}}
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-card__image-link">
                                        @if($product->videos_count > 0 && $product->first_video)
                                            {{-- Afficher la vidéo si disponible --}}
                                            <div class="product-card__image-wrapper" style="position: relative;">
                                                <video
                                                    src="{{ $product->first_video }}"
                                                    class="product-card__video"
                                                    muted
                                                    loop
                                                    playsinline
                                                    preload="metadata"
                                                    onmouseenter="this.play()"
                                                    onmouseleave="this.pause(); this.currentTime=0;"
                                                    style="width: 100%; height: 100%; object-fit: cover;"
                                                >
                                                </video>
                                                {{-- Badge vidéo --}}
                                                <span class="product-card__video-badge" style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; display: flex; align-items: center; gap: 4px; z-index: 10;">
                                                    <span style="font-size: 16px;">▶️</span>
                                                    <span>Vidéo</span>
                                                </span>
                                            </div>
                                        @elseif($product->first_image)
                                            {{-- Afficher l'image si pas de vidéo --}}
                                            <div class="product-card__image-wrapper">
                                                <img
                                                    src="{{ $product->first_image }}"
                                                    alt="{{ $product->name }}"
                                                    class="product-card__image"
                                                >
                                            </div>
                                        @else
                                            {{-- Placeholder si ni vidéo ni image --}}
                                            <div class="product-card__image-placeholder">
                                                <span class="product-card__image-placeholder-icon">📦</span>
                                            </div>
                                        @endif
                                    </a>

                                    {{-- Contenu --}}
                                    <div class="product-card__body">
                                        <h3 class="product-card__title">
                                            {{ $product->name }}
                                        </h3>

                                        <p class="product-card__description">
                                            {{ Str::limit($product->description, 80) }}
                                        </p>

                                        @if($product->current_price)
                                            <div class="product-card__price-block">
                                                <span class="product-card__price">
                                                    {{ $product->formatted_promotional_price ?? $product->formatted_price }}
                                                </span>
                                                @if($product->is_on_sale)
                                                    <span class="product-card__price-old">
                                                        {{ $product->formatted_price }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Boutons Voir + Commander --}}
                                        <div class="product-card__actions">
                                            <a href="{{ route('products.show', $product->slug) }}"
                                               class="product-card__btn product-card__btn--ghost">
                                                <span class="product-card__btn-icon">👁️</span>
                                                <span>Voir</span>
                                            </a>

                                            @if($product->stock_quantity > 0)
                                                <a href="{{ route('orders.create', ['product_id' => $product->id]) }}"
                                                   class="product-card__btn product-card__btn--primary">
                                                    <span class="product-card__btn-icon">🛒</span>
                                                    <span>Commander</span>
                                                </a>
                                            @else
                                                <span class="product-card__btn product-card__btn--disabled">
                                                    ⛔ Rupture
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($products->hasPages())
                            <div class="products-pagination">
                                {{ $products->links() }}
                            </div>
                        @endif
                    @else
                        <div class="products-empty">
                            <svg class="products-empty__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <h3 class="products-empty__title">Aucun produit trouvé</h3>
                            <p class="products-empty__text">Essayez de modifier vos critères de recherche</p>
                            <a href="{{ route('products.index') }}" class="products-empty__button">
                                Voir tous les produits
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Contact --}}
    <section class="products-cta">
        <div class="products-cta__container">
            <h2 class="products-cta__title">Besoin d'un devis personnalisé ?</h2>
            <p class="products-cta__subtitle">
                Contactez Jackson Energy pour une étude gratuite de vos besoins énergétiques
            </p>
            <div class="products-cta__buttons">
                <a href="tel:+22677126519" class="products-cta__btn products-cta__btn--light">
                    📞 Appeler maintenant
                </a>
                <a href="https://wa.me/22663952032" target="_blank"
                   class="products-cta__btn products-cta__btn--whatsapp">
                    💬 WhatsApp
                </a>
                <a href="{{ route('contact') }}" class="products-cta__btn products-cta__btn--primary">
                    📧 Formulaire de contact
                </a>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.app', [
    'title' => $product->meta_title ?: $product->name . ' - Jackson Energy International',
    'description' => $product->meta_description ?: Str::limit(strip_tags($product->description), 160),
])

@section('content')
    {{-- HEADER PRODUIT --}}
    <section class="product-header">
        <div class="product-header__container">
            <nav class="product-breadcrumb">
                <a href="{{ route('home') }}" class="product-breadcrumb__link">Accueil</a>
                <span class="product-breadcrumb__separator">/</span>
                <a href="{{ route('products.index') }}" class="product-breadcrumb__link">Produits</a>
                <span class="product-breadcrumb__separator">/</span>
                <span class="product-breadcrumb__current">{{ $product->name }}</span>
            </nav>

            <div class="product-header__top">
                <span class="product-header__category">
                    {{ $product->category->name ?? 'Produit' }}
                </span>

                @if($product->is_featured ?? false)
                    <span class="product-header__badge">Vedette</span>
                @endif
            </div>

            <h1 class="product-header__title">
                {{ $product->name }}
            </h1>

            <p class="product-header__subtitle">
                {{ Str::limit(strip_tags($product->description), 140) }}
            </p>
        </div>
    </section>

    {{-- CONTENU PRODUIT --}}
    <section class="product-section">
        <div class="product-section__container">
            <div class="product-layout">
                {{-- Colonne gauche : médias --}}
                <div class="product-media">
                    <div class="product-media__main">
                        @if($product->images_count > 0)
                            <img
                                src="{{ $product->first_image }}"
                                alt="{{ $product->name }}"
                                class="product-media__main-image"
                                id="product-main-image"
                            >
                        @else
                            <div class="product-media__placeholder">
                                <span class="product-media__placeholder-icon">🖼️</span>
                                <span class="product-media__placeholder-text">Aucune image disponible</span>
                            </div>
                        @endif

                        @if($product->promotional_price)
                            <span class="product-media__badge-discount">
                                -{{ round(($product->price - $product->promotional_price) / $product->price * 100) }}%
                            </span>
                        @endif
                    </div>

                    @if($product->images_count > 1)
                        <div class="product-thumbs">
                            @foreach($product->image_urls as $index => $imageUrl)
                                <button
                                    type="button"
                                    class="product-thumbs__item {{ $index === 0 ? 'product-thumbs__item--active' : '' }}"
                                    onclick="changeProductImage('{{ $imageUrl }}', {{ $index }})"
                                >
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                        class="product-thumbs__img"
                                    >
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Colonne droite : infos --}}
                <div class="product-info">
                    <div class="product-pricing">
                        <div class="product-pricing__prices">
                            @if($product->promotional_price)
                                <div class="product-pricing__current">
                                    {{ number_format($product->promotional_price, 0, ',', ' ') }} FCFA
                                </div>
                                <div class="product-pricing__old">
                                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                </div>
                            @else
                                <div class="product-pricing__current">
                                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                </div>
                            @endif
                        </div>

                        <div class="product-pricing__stock">
                            @if($product->stock_quantity > 0)
                                <span class="product-pricing__stock-badge product-pricing__stock-badge--ok">
                                    ● En stock – {{ $product->stock_quantity }} unité(s) disponible(s)
                                </span>
                            @else
                                <span class="product-pricing__stock-badge product-pricing__stock-badge--out">
                                    ● Rupture de stock
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($product->description)
                        <div class="product-block">
                            <h2 class="product-block__title">Description</h2>
                            <div class="product-block__content">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($product->specifications)
                        <div class="product-block">
                            <h2 class="product-block__title">Spécifications techniques</h2>
                            <div class="product-block__content">
                                {!! nl2br(e($product->specifications)) !!}
                            </div>
                        </div>
                    @endif

                    @if($product->warranty)
                        <div class="product-warranty">
                            <h3 class="product-warranty__title">Garantie</h3>
                            <p class="product-warranty__text">
                                {{ $product->warranty }}
                            </p>
                        </div>
                    @endif

                    {{-- Boutons d’action --}}
                    <div class="product-actions">
                        @if($product->stock_quantity > 0)
                            <a href="{{ route('orders.create', ['product_id' => $product->id]) }}"
                               class="product-actions__btn product-actions__btn--primary">
                                <span class="product-actions__icon">🛒</span>
                                <span>Commander ce produit</span>
                            </a>
                        @else
                            <span class="product-actions__btn product-actions__btn--disabled">
                                ⛔ Produit en rupture de stock
                            </span>
                        @endif

                        <div class="product-actions__secondary">
                            <a href="tel:+22665033700"
                               class="product-actions__btn product-actions__btn--phone">
                                <span class="product-actions__icon">📞</span>
                                <span>Appeler</span>
                            </a>

                            <a href="https://wa.me/22665033700?text={{ urlencode('Bonjour, je suis intéressé par '.$product->name.' - Prix '.number_format($product->current_price, 0, ',', ' ').' FCFA') }}"
                               target="_blank"
                               class="product-actions__btn product-actions__btn--whatsapp">
                                <span class="product-actions__icon">💬</span>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>

                    <div class="product-extra">
                        <h3 class="product-extra__title">Informations utiles</h3>
                        <ul class="product-extra__list">
                            <li class="product-extra__item">
                                <span class="product-extra__icon">🚚</span>
                                <div>
                                    <p class="product-extra__item-title">Livraison disponible</p>
                                    <p class="product-extra__item-text">
                                        Ouagadougou et dans tout le Burkina Faso
                                    </p>
                                </div>
                            </li>
                            <li class="product-extra__item">
                                <span class="product-extra__icon">💳</span>
                                <div>
                                    <p class="product-extra__item-title">Paiements acceptés</p>
                                    <p class="product-extra__item-text">
                                        Espèces, Orange Money, Moov Money, Virement bancaire
                                    </p>
                                </div>
                            </li>
                            <li class="product-extra__item">
                                <span class="product-extra__icon">🛠️</span>
                                <div>
                                    <p class="product-extra__item-title">Services inclus</p>
                                    <p class="product-extra__item-text">
                                        Installation professionnelle et maintenance disponibles
                                    </p>
                                </div>
                            </li>
                            <li class="product-extra__item">
                                <span class="product-extra__icon">🤝</span>
                                <div>
                                    <p class="product-extra__item-title">Support technique</p>
                                    <p class="product-extra__item-text">
                                        Assistance et conseil personnalisé gratuits
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Produits similaires --}}
            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
                <div class="product-related">
                    <div class="product-related__header">
                        <h2 class="product-related__title">Produits similaires</h2>
                        <p class="product-related__subtitle">
                            Découvrez d'autres produits qui pourraient vous intéresser
                        </p>
                    </div>

                    <div class="product-related__grid">
                        @foreach($relatedProducts as $related)
                            <a href="{{ route('products.show', $related->slug) }}"
                               class="product-related-card">
                                <div class="product-related-card__image-wrapper">
                                    @if($related->first_image)
                                        <img
                                            src="{{ $related->first_image }}"
                                            alt="{{ $related->name }}"
                                            class="product-related-card__image"
                                        >
                                    @else
                                        <div class="product-related-card__image-placeholder">
                                            <span>🖼️</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="product-related-card__body">
                                    <h3 class="product-related-card__title">
                                        {{ $related->name }}
                                    </h3>
                                    <p class="product-related-card__price">
                                        {{ number_format($related->current_price, 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <script>
        function changeProductImage(url, index) {
            const main = document.getElementById('product-main-image');
            if (!main) return;
            main.src = url;

            const items = document.querySelectorAll('.product-thumbs__item');
            items.forEach((btn, i) => {
                if (i === index) {
                    btn.classList.add('product-thumbs__item--active');
                } else {
                    btn.classList.remove('product-thumbs__item--active');
                }
            });
        }
    </script>
@endsection

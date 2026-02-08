@extends('layouts.app')

@section('title', 'Passer une commande - Jackson Energy International')

@section('content')
{{-- Hero Header --}}
<div class="bg-gradient-to-r from-green-600 to-green-700 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl font-montserrat font-bold text-white mb-3">
                🛒 Finaliser votre commande
            </h1>
            <p class="text-green-100 text-lg">
                Remplissez le formulaire ci-dessous pour commander vos produits en toute sécurité
            </p>
            <div class="mt-6 flex items-center justify-center gap-3 text-white text-sm">
                <div class="flex items-center gap-2">
                    <span class="bg-white/20 rounded-full px-3 py-1">✅</span>
                    <span>Paiement sécurisé</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-white/20 rounded-full px-3 py-1">🚚</span>
                    <span>Livraison rapide</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="bg-white/20 rounded-full px-3 py-1">💬</span>
                    <span>Support 24/7</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Progress Steps --}}
<div class="bg-white border-b">
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-bold">1</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-900">Informations</p>
                        <p class="text-xs text-gray-500">Vos coordonnées</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold">2</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-500">Paiement</p>
                        <p class="text-xs text-gray-400">Méthode de paiement</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold">3</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-500">Confirmation</p>
                        <p class="text-xs text-gray-400">Récapitulatif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Form (étape 1) --}}
<div class="container mx-auto px-4 py-12">
    <form method="POST" action="{{ route('orders.store') }}" class="max-w-6xl mx-auto" id="orderForm">
        @csrf
        @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
        <div class="flex items-start">
            <span class="text-2xl mr-3">⚠️</span>
            <div class="flex-1">
                <p class="font-semibold mb-2">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

        {{-- Messages d'erreur globaux --}}
        @if($errors->any())
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
                <div class="flex items-start">
                    <span class="text-2xl mr-3">⚠️</span>
                    <div class="flex-1">
                        <p class="font-semibold mb-2">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Colonne principale (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informations client --}}
                <div class="bg-white rounded-xl shadow-lg p-8 border-l-4 border-blue-500">
                    <h2 class="text-2xl font-montserrat font-bold mb-6 text-gray-900 flex items-center">
                        <span class="bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center mr-3 text-lg">👤</span>
                        Informations client
                    </h2>

                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="customer_name" class="block text-sm font-bold text-gray-700 mb-2">
                                Nom complet <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="customer_name"
                                name="customer_name"
                                value="{{ old('customer_name') }}"
                                required
                                placeholder="Ex: Jean Ouédraogo"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('customer_name') border-red-500 @enderror"
                            >
                            @error('customer_name')
                                <p class="text-red-500 text-sm mt-1 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer_phone" class="block text-sm font-bold text-gray-700 mb-2">
                                Téléphone <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="tel"
                                id="customer_phone"
                                name="customer_phone"
                                value="{{ old('customer_phone') }}"
                                required
                                placeholder="+226 XX XX XX XX"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('customer_phone') border-red-500 @enderror"
                            >
                            @error('customer_phone')
                                <p class="text-red-500 text-sm mt-1 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="customer_email" class="block text-sm font-bold text-gray-700 mb-2">
                                Email
                            </label>
                            <input
                                type="email"
                                id="customer_email"
                                name="customer_email"
                                value="{{ old('customer_email') }}"
                                placeholder="email@exemple.com"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            >
                        </div>

                        <div class="md:col-span-2">
                            <label for="customer_company" class="block text-sm font-bold text-gray-700 mb-2">
                                Entreprise / Organisation
                            </label>
                            <input
                                type="text"
                                id="customer_company"
                                name="customer_company"
                                value="{{ old('customer_company') }}"
                                placeholder="Nom de votre entreprise (optionnel)"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            >
                        </div>
                    </div>
                </div>

                {{-- Informations de livraison --}}
                <div class="bg-white rounded-xl shadow-lg p-8 border-l-4 border-purple-500">
                    <h2 class="text-2xl font-montserrat font-bold mb-6 text-gray-900 flex items-center">
                        <span class="bg-purple-100 text-purple-600 rounded-full w-10 h-10 flex items-center justify-center mr-3 text-lg">🚚</span>
                        Informations de livraison
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <label for="delivery_address" class="block text-sm font-bold text-gray-700 mb-2">
                                Adresse de livraison <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="delivery_address"
                                name="delivery_address"
                                rows="3"
                                required
                                placeholder="Entrez l'adresse complète de livraison..."
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('delivery_address') border-red-500 @enderror"
                            >{{ old('delivery_address') }}</textarea>
                            @error('delivery_address')
                                <p class="text-red-500 text-sm mt-1 flex items-center">
                                    <span class="mr-1">⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="delivery_city" class="block text-sm font-bold text-gray-700 mb-2">
                                    Ville <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="delivery_city"
                                    name="delivery_city"
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('delivery_city') border-red-500 @enderror"
                                >
                                    <option value="">Sélectionnez une ville</option>
                                    <option value="Ouagadougou" {{ old('delivery_city') == 'Ouagadougou' ? 'selected' : '' }}>🏙️ Ouagadougou</option>
                                    <option value="Bobo-Dioulasso" {{ old('delivery_city') == 'Bobo-Dioulasso' ? 'selected' : '' }}>🏙️ Bobo-Dioulasso</option>
                                    <option value="Koudougou" {{ old('delivery_city') == 'Koudougou' ? 'selected' : '' }}>🏙️ Koudougou</option>
                                    <option value="Banfora" {{ old('delivery_city') == 'Banfora' ? 'selected' : '' }}>🏙️ Banfora</option>
                                    <option value="Tenkodogo" {{ old('delivery_city') == 'Tenkodogo' ? 'selected' : '' }}>🏙️ Tenkodogo</option>
                                    <option value="Fada N'Gourma" {{ old('delivery_city') == "Fada N'Gourma" ? 'selected' : '' }}>🏙️ Fada N'Gourma</option>
                                    <option value="Dori" {{ old('delivery_city') == 'Dori' ? 'selected' : '' }}>🏙️ Dori</option>
                                    <option value="Autre" {{ old('delivery_city') == 'Autre' ? 'selected' : '' }}>📍 Autre ville</option>
                                </select>
                                @error('delivery_city')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <span class="mr-1">⚠️</span> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="delivery_phone" class="block text-sm font-bold text-gray-700 mb-2">
                                    Téléphone de livraison <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="tel"
                                    id="delivery_phone"
                                    name="delivery_phone"
                                    value="{{ old('delivery_phone') }}"
                                    required
                                    placeholder="+226 XX XX XX XX"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('delivery_phone') border-red-500 @enderror"
                                >
                                @error('delivery_phone')
                                    <p class="text-red-500 text-sm mt-1 flex items-center">
                                        <span class="mr-1">⚠️</span> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes supplémentaires --}}
                <div class="bg-white rounded-xl shadow-lg p-8 border-l-4 border-yellow-500">
                    <h2 class="text-2xl font-montserrat font-bold mb-6 text-gray-900 flex items-center">
                        <span class="bg-yellow-100 text-yellow-600 rounded-full w-10 h-10 flex items-center justify-center mr-3 text-lg">📝</span>
                        Notes supplémentaires
                    </h2>

                    <textarea
                        id="notes"
                        name="notes"
                        rows="4"
                        placeholder="Ajoutez des instructions spéciales pour votre commande (optionnel)..."
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    >{{ old('notes') }}</textarea>
                </div>
            </div>

            {{-- Colonne latérale - Récapitulatif (1/3) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    {{-- Produits sélectionnés --}}
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border-t-4 border-green-500">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <span class="text-2xl mr-2">🛍️</span> Votre panier
                        </h3>

                        <div id="products-container" class="space-y-4">
                            @if($product)
                                {{-- Produit pré-sélectionné --}}
                                <div class="product-item border border-gray-200 rounded-lg p-4 hover:border-green-500 transition">
                                    <div class="flex items-start space-x-3">
                                        <img
                                            src="{{ $product->first_image }}"
                                            alt="{{ $product->name }}"
                                            class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200"
                                        >
                                        <div class="flex-1">
                                            <h4 class="font-bold text-sm text-gray-900">{{ $product->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Non catégorisé' }}</p>
                                            <p class="text-lg font-bold text-green-600 mt-1">
                                                {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <label class="text-xs font-semibold text-gray-700">Quantité:</label>
                                        <input
                                            type="number"
                                            name="products[0][quantity]"
                                            value="{{ old('products.0.quantity', 1) }}"
                                            min="1"
                                            max="{{ $product->stock_quantity }}"
                                            class="w-20 px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 text-center font-bold"
                                            data-price="{{ $product->price }}"
                                            onchange="updateTotal()"
                                        >
                                        <input type="hidden" name="products[0][id]" value="{{ $product->id }}">
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="text-5xl mb-3 opacity-30">🛒</div>
                                    <p class="text-gray-500 mb-4 font-medium">Votre panier est vide</p>
                                    <a
                                        href="{{ route('products.index') }}"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition"
                                    >
                                        <span class="mr-2">🛍️</span> Choisir des produits
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if($product)
                            {{-- Total --}}
                            <div class="mt-6 pt-4 border-t-2 border-gray-200">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Sous-total:</span>
                                    <span class="font-bold text-gray-900" id="subtotal">
                                        {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-gray-600">Frais de livraison:</span>
                                    <span class="font-bold text-green-600">Gratuit</span>
                                </div>
                                <div class="flex justify-between items-center pt-3 border-t-2 border-green-200">
                                    <span class="text-lg font-bold text-gray-900">Total:</span>
                                    <span class="text-2xl font-bold text-green-600" id="total">
                                        {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Garanties --}}
                    <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">🛡️ Nos garanties</h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Paiement 100% sécurisé</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Livraison rapide et suivie</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Support client réactif</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Produits garantis qualité</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-8 max-w-6xl">
            <a
                href="{{ route('products.index') }}"
                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors"
            >
                ← Continuer les achats
            </a>
            <button
                type="submit"
                class="w-full sm:w-auto inline-flex justify-center items-center px-10 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105"
            >
                <span class="mr-2">➡️</span> Continuer vers le paiement
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function updateTotal() {
    const quantityInputs = document.querySelectorAll('input[name^="products"][name$="[quantity]"]');
    let total = 0;

    quantityInputs.forEach(input => {
        const price = parseFloat(input.dataset.price);
        const quantity = parseInt(input.value) || 0;
        total += price * quantity;
    });

    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');

    if (subtotalEl && totalEl) {
        const formatted = new Intl.NumberFormat('fr-FR').format(total);
        subtotalEl.textContent = formatted + ' FCFA';
        totalEl.textContent = formatted + ' FCFA';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
        orderForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2">⏳</span> Traitement en cours...';
            }
        });
    }
});
</script>
@endpush
@endsection

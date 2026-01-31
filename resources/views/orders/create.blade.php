@extends('layouts.public')

@section('title', 'Passer une commande - Grossiste Ouaga International')

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-montserrat font-bold text-gris-moderne mb-4">
            Passer une commande
        </h1>
        <p class="text-gray-600">Remplissez le formulaire ci-dessous pour commander vos produits</p>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <form method="POST" action="{{ route('orders.store') }}" class="max-w-4xl mx-auto">
        @csrf
        
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Informations client -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-montserrat font-bold mb-6 text-gris-moderne">
                    📋 Informations client
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet *</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('customer_name') border-red-500 @enderror">
                        @error('customer_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="+226 XX XX XX XX" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('customer_phone') border-red-500 @enderror">
                        @error('customer_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                    </div>
                    
                    <div>
                        <label for="customer_company" class="block text-sm font-medium text-gray-700 mb-2">Entreprise</label>
                        <input type="text" id="customer_company" name="customer_company" value="{{ old('customer_company') }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                    </div>
                </div>
            </div>
            
            <!-- Informations de livraison -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-montserrat font-bold mb-6 text-gris-moderne">
                    🚚 Informations de livraison
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="delivery_address" class="block text-sm font-medium text-gray-700 mb-2">Adresse de livraison *</label>
                        <textarea id="delivery_address" name="delivery_address" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('delivery_address') border-red-500 @enderror">{{ old('delivery_address') }}</textarea>
                        @error('delivery_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="delivery_city" class="block text-sm font-medium text-gray-700 mb-2">Ville *</label>
                        <select id="delivery_city" name="delivery_city" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('delivery_city') border-red-500 @enderror">
                            <option value="">Sélectionnez une ville</option>
                            <option value="Ouagadougou" {{ old('delivery_city') == 'Ouagadougou' ? 'selected' : '' }}>Ouagadougou</option>
                            <option value="Bobo-Dioulasso" {{ old('delivery_city') == 'Bobo-Dioulasso' ? 'selected' : '' }}>Bobo-Dioulasso</option>
                            <option value="Koudougou" {{ old('delivery_city') == 'Koudougou' ? 'selected' : '' }}>Koudougou</option>
                            <option value="Banfora" {{ old('delivery_city') == 'Banfora' ? 'selected' : '' }}>Banfora</option>
                            <option value="Tenkodogo" {{ old('delivery_city') == 'Tenkodogo' ? 'selected' : '' }}>Tenkodogo</option>
                            <option value="Autre" {{ old('delivery_city') == 'Autre' ? 'selected' : '' }}>Autre ville</option>
                        </select>
                        @error('delivery_city')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="delivery_phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone pour la livraison *</label>
                        <input type="tel" id="delivery_phone" name="delivery_phone" value="{{ old('delivery_phone') }}" required placeholder="+226 XX XX XX XX" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('delivery_phone') border-red-500 @enderror">
                        @error('delivery_phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sélection des produits -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
            <h2 class="text-xl font-montserrat font-bold mb-6 text-gris-moderne">
                🛍️ Sélection des produits
            </h2>
            
            <div id="products-container">
                @if($product)
                <!-- Produit pré-sélectionné -->
                <div class="product-item border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded-lg">
                        <div class="flex-1">
                            <h3 class="font-semibold">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
                            <p class="text-lg font-bold text-vert-energie">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-sm font-medium">Quantité:</label>
                            <input type="number" name="products[0][quantity]" value="1" min="1" max="{{ $product->stock_quantity }}" class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                            <input type="hidden" name="products[0][id]" value="{{ $product->id }}">
                        </div>
                    </div>
                </div>
                @else
                <!-- Sélection manuelle de produits -->
                <div class="text-center py-8">
                    <div class="text-4xl mb-4">🛍️</div>
                    <p class="text-gray-600 mb-4">Aucun produit sélectionné</p>
                    <a href="{{ route('products.index') }}" class="bg-vert-energie text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                        Choisir des produits
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Méthode de paiement -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
            <h2 class="text-xl font-montserrat font-bold mb-6 text-gris-moderne">
                💳 Méthode de paiement
            </h2>
            
            <div class="grid md:grid-cols-2 gap-4">
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 payment-method">
                    <input type="radio" name="payment_method" value="orange_money" class="mr-3" required>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">O</div>
                        <span class="font-medium">Orange Money</span>
                    </div>
                </label>
                
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 payment-method">
                    <input type="radio" name="payment_method" value="moov_money" class="mr-3" required>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">M</div>
                        <span class="font-medium">Moov Money</span>
                    </div>
                </label>
                
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 payment-method">
                    <input type="radio" name="payment_method" value="bank_transfer" class="mr-3" required>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">🏦</div>
                        <span class="font-medium">Virement bancaire</span>
                    </div>
                </label>
                
                <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 payment-method">
                    <input type="radio" name="payment_method" value="cash" class="mr-3" required>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3">💵</div>
                        <span class="font-medium">Espèces à la livraison</span>
                    </div>
                </label>
            </div>
            
            <div id="payment-phone-field" class="mt-4 hidden">
                <label for="payment_phone" class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone pour le paiement *</label>
                <input type="tel" id="payment_phone" name="payment_phone" value="{{ old('payment_phone') }}" placeholder="+226 XX XX XX XX" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
            </div>
        </div>
        
        <!-- Notes -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-8">
            <h2 class="text-xl font-montserrat font-bold mb-6 text-gris-moderne">
                📝 Notes supplémentaires
            </h2>
            
            <textarea id="notes" name="notes" rows="3" placeholder="Ajoutez des instructions spéciales pour votre commande..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('notes') }}</textarea>
        </div>
        
        <!-- Boutons -->
        <div class="flex justify-between items-center mt-8">
            <a href="{{ route('products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition">
                ← Continuer les achats
            </a>
            <button type="submit" class="bg-vert-energie text-white px-8 py-3 rounded-lg hover:bg-green-700 transition font-semibold text-lg">
                📋 Passer la commande
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentPhoneField = document.getElementById('payment-phone-field');
    const paymentPhoneInput = document.getElementById('payment_phone');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            if (this.value === 'orange_money' || this.value === 'moov_money') {
                paymentPhoneField.classList.remove('hidden');
                paymentPhoneInput.required = true;
            } else {
                paymentPhoneField.classList.add('hidden');
                paymentPhoneInput.required = false;
            }
        });
    });
});
</script>
@endpush
@endsection

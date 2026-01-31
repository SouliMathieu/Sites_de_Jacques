@extends('admin.layouts.app')

@section('title', 'Commande ' . $order->order_number . ' - Administration')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-montserrat font-bold text-gray-900">Commande {{ $order->order_number }}</h1>
        <p class="text-gray-600">Détails complets de la commande</p>
    </div>
    <div class="flex space-x-3">
        <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer->phone) }}?text=Bonjour {{ $order->customer->name }}, concernant votre commande {{ $order->order_number }}..." class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
            💬 Contacter le client
        </a>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            ← Retour à la liste
        </a>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-8">
    <!-- Informations principales -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Statut et actions rapides -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">🎯 Statut et actions</h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Statut de la commande -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut de la commande</label>
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="flex space-x-2">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Confirmée</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🔄 En préparation</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>🚚 Expédiée</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>📦 Livrée</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Annulée</option>
                        </select>
                        <button type="submit" class="bg-vert-energie text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                            Mettre à jour
                        </button>
                    </form>
                </div>
                
                <!-- Statut du paiement -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut du paiement</label>
                    <form method="POST" action="{{ route('admin.orders.update-payment-status', $order) }}" class="flex space-x-2">
                        @csrf
                        @method('PATCH')
                        <select name="payment_status" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                            <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>✅ Payé</option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>❌ Échoué</option>
                        </select>
                        <button type="submit" class="bg-bleu-tech text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Produits commandés -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">🛍️ Produits commandés</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($order->orderItems as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="{{ $item->product->first_image }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded-lg mr-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->product->category->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                Total de la commande :
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-vert-energie">
                                {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Notes -->
        @if($order->notes)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">📝 Notes du client</h2>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-gray-700">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
    </div>
    
    <!-- Informations latérales -->
    <div class="space-y-6">
        <!-- Informations client -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">👤 Informations client</h2>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Nom</label>
                    <p class="text-lg font-medium">{{ $order->customer->name }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Téléphone</label>
                    <div class="flex items-center space-x-2">
                        <p class="text-lg">{{ $order->customer->phone }}</p>
                        <a href="tel:{{ $order->customer->phone }}" class="text-blue-600 hover:text-blue-800">📞</a>
                        <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer->phone) }}" class="text-green-600 hover:text-green-800">💬</a>
                    </div>
                </div>
                
                @if($order->customer->email)
                <div>
                    <label class="text-sm font-medium text-gray-600">Email</label>
                    <p class="text-lg">{{ $order->customer->email }}</p>
                </div>
                @endif
                
                @if($order->customer->company)
                <div>
                    <label class="text-sm font-medium text-gray-600">Entreprise</label>
                    <p class="text-lg">{{ $order->customer->company }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Informations de livraison -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">🚚 Livraison</h2>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Adresse</label>
                    <p class="text-gray-700">{{ $order->delivery_address }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Ville</label>
                    <p class="text-lg">{{ $order->delivery_city }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Téléphone de livraison</label>
                    <div class="flex items-center space-x-2">
                        <p class="text-lg">{{ $order->delivery_phone }}</p>
                        <a href="tel:{{ $order->delivery_phone }}" class="text-blue-600 hover:text-blue-800">📞</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Informations de paiement -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">💳 Paiement</h2>
            
            <div class="space-y-3">
                <div>
                    <label class="text-sm font-medium text-gray-600">Méthode</label>
                    <div class="flex items-center space-x-2">
                        @switch($order->payment_method)
                            @case('orange_money')
                                <div class="w-6 h-6 bg-orange-500 rounded-full"></div>
                                <span>Orange Money</span>
                                @break
                            @case('moov_money')
                                <div class="w-6 h-6 bg-blue-600 rounded-full"></div>
                                <span>Moov Money</span>
                                @break
                            @case('bank_transfer')
                                <div class="w-6 h-6 bg-green-600 rounded-full"></div>
                                <span>Virement bancaire</span>
                                @break
                            @case('cash')
                                <div class="w-6 h-6 bg-gray-600 rounded-full"></div>
                                <span>Espèces</span>
                                @break
                        @endswitch
                    </div>
                </div>
                
                @if($order->payment_phone)
                <div>
                    <label class="text-sm font-medium text-gray-600">Numéro de paiement</label>
                    <p class="text-lg">{{ $order->payment_phone }}</p>
                </div>
                @endif
                
                @if($order->payment_reference)
                <div>
                    <label class="text-sm font-medium text-gray-600">Référence</label>
                    <p class="text-lg font-mono bg-gray-100 px-2 py-1 rounded">{{ $order->payment_reference }}</p>
                </div>
                @endif
                
                <div>
                    <label class="text-sm font-medium text-gray-600">Statut</label>
                    <span class="px-3 py-1 rounded-full text-sm {{ $order->payment_status_badge }}">
                        @switch($order->payment_status)
                            @case('pending') ⏳ En attente @break
                            @case('paid') ✅ Payé @break
                            @case('failed') ❌ Échoué @break
                        @endswitch
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Informations techniques -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">⚙️ Informations techniques</h2>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">ID Commande:</span>
                    <span class="font-mono">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Créée le:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Modifiée le:</span>
                    <span>{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Nombre d'articles:</span>
                    <span>{{ $order->orderItems->sum('quantity') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">⚡ Actions rapides</h2>
            
            <div class="space-y-3">
                <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer->phone) }}?text=Bonjour {{ $order->customer->name }}, votre commande {{ $order->order_number }} est prête pour la livraison !" class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition text-center block">
                    💬 Notifier livraison
                </a>
                
                <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer->phone) }}?text=Bonjour {{ $order->customer->name }}, votre commande {{ $order->order_number }} a été livrée avec succès. Merci pour votre confiance !" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition text-center block">
                    📦 Confirmer livraison
                </a>
                
                <button onclick="window.print()" class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">
                    🖨️ Imprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

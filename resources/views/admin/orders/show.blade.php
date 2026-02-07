@extends('admin.layouts.app')

@section('title', 'Commande ' . $order->order_number . ' - Jackson Energy International')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header avec actions --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-montserrat font-bold text-gray-900">
                        📋 Commande {{ $order->order_number }}
                    </h1>
                    <span class="px-4 py-1.5 rounded-full text-sm font-bold
                        @switch($order->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('confirmed') bg-blue-100 text-blue-800 @break
                            @case('processing') bg-indigo-100 text-indigo-800 @break
                            @case('shipped') bg-purple-100 text-purple-800 @break
                            @case('delivered') bg-green-100 text-green-800 @break
                            @case('completed') bg-emerald-100 text-emerald-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                        @endswitch">
                        @switch($order->status)
                            @case('pending') ⏳ En attente @break
                            @case('confirmed') ✅ Confirmée @break
                            @case('processing') 🔄 En traitement @break
                            @case('shipped') 🚚 Expédiée @break
                            @case('delivered') 📦 Livrée @break
                            @case('completed') ✅ Complétée @break
                            @case('cancelled') ❌ Annulée @break
                        @endswitch
                    </span>
                </div>
                <p class="text-gray-600">Créée le {{ $order->created_at->format('d/m/Y à H:i') }} • {{ $order->created_at->diffForHumans() }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer_phone) }}?text=Bonjour {{ urlencode($order->customer_name) }}, concernant votre commande {{ $order->order_number }}..." 
                   class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all"
                   target="_blank">
                    <span class="mr-2">💬</span> WhatsApp Client
                </a>
                <a href="{{ route('admin.orders.print', $order) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all"
                   target="_blank">
                    <span class="mr-2">🖨️</span> Imprimer
                </a>
                <a href="{{ route('admin.orders.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                    ← Retour
                </a>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Statut et actions --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">🎯</span>
                    Gestion des statuts
                </h2>
                
                <div class="grid md:grid-cols-2 gap-6">
                    {{-- Statut commande --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">📦 Statut de la commande</label>
                        <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>✅ Confirmée</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🔄 En traitement</option>
                                <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>🚚 Expédiée</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>📦 Livrée</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>✅ Complétée</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>❌ Annulée</option>
                            </select>
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                                Mettre à jour
                            </button>
                        </form>
                    </div>
                    
                    {{-- Statut paiement --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">💳 Statut du paiement</label>
                        <form method="POST" action="{{ route('admin.orders.update-payment-status', $order) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')
                            <select name="payment_status" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>✅ Payé</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>❌ Échoué</option>
                            </select>
                            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                                Mettre à jour
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            {{-- Produits commandés --}}
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border-l-4 border-green-500">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">🛍️</span>
                        Produits commandés ({{ $order->orderItems->count() }})
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Produit</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase">Prix Unit.</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Qté</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->orderItems as $item)
                            <tr class="hover:bg-green-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-14 w-14">
                                            @if($item->product && $item->product->hasImages())
                                                <img src="{{ $item->product->first_image }}" 
                                                     alt="{{ $item->product_name }}" 
                                                     class="h-14 w-14 rounded-lg object-cover border-2 border-gray-200 shadow-sm">
                                            @else
                                                <div class="h-14 w-14 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                    <span class="text-2xl">📦</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $item->product_name }}</div>
                                            @if($item->product)
                                                <div class="text-xs text-gray-500 mt-1">{{ $item->product->category->name ?? 'Non catégorisé' }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    {{ number_format($item->unit_price, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                        {{ $item->quantity }}×
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-green-600">
                                    {{ number_format($item->total_price, 0, ',', ' ') }} F
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gradient-to-r from-green-50 to-emerald-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-base font-bold text-gray-900">
                                    💰 TOTAL DE LA COMMANDE
                                </td>
                                <td class="px-6 py-4 text-right text-xl font-bold text-green-600">
                                    {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            {{-- Notes --}}
            @if($order->notes)
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <span class="bg-yellow-100 text-yellow-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">📝</span>
                    Notes du client
                </h2>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-gray-700 italic">{{ $order->notes }}</p>
                </div>
            </div>
            @endif
        </div>
        
        {{-- Colonne latérale --}}
        <div class="space-y-6">
            {{-- Informations client --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-blue-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">👤</span> Informations client
                </h2>
                
                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom complet</label>
                        <p class="text-lg font-bold text-gray-900 mt-1">{{ $order->customer_name }}</p>
                    </div>
                    
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Téléphone</label>
                        <div class="flex items-center gap-3 mt-1">
                            <p class="text-lg font-semibold text-gray-900">{{ $order->customer_phone }}</p>
                            <a href="tel:{{ $order->customer_phone }}" 
                               class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110"
                               title="Appeler">
                                <span class="text-xl">📞</span>
                            </a>
                            <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer_phone) }}" 
                               class="text-green-600 hover:text-green-800 transition transform hover:scale-110"
                               title="WhatsApp"
                               target="_blank">
                                <span class="text-xl">💬</span>
                            </a>
                        </div>
                    </div>
                    
                    @if($order->customer_email)
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email</label>
                        <p class="text-sm text-gray-900 mt-1">{{ $order->customer_email }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Informations livraison --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-purple-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">🚚</span> Livraison
                </h2>
                
                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Adresse</label>
                        <p class="text-gray-900 mt-1 leading-relaxed">{{ $order->delivery_address }}</p>
                    </div>
                    
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ville</label>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $order->delivery_city }}</p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Téléphone de livraison</label>
                        <div class="flex items-center gap-3 mt-1">
                            <p class="text-lg font-semibold text-gray-900">{{ $order->delivery_phone }}</p>
                            <a href="tel:{{ $order->delivery_phone }}" 
                               class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110"
                               title="Appeler">
                                <span class="text-xl">📞</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Informations paiement --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-green-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">💳</span> Paiement
                </h2>
                
                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Méthode</label>
                        <div class="flex items-center gap-3 mt-2">
                            @switch($order->payment_method)
                                @case('orange_money')
                                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center shadow-md">
                                        <span class="text-white text-xs font-bold">OM</span>
                                    </div>
                                    <span class="font-semibold text-gray-900">Orange Money</span>
                                    @break
                                @case('moov_money')
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center shadow-md">
                                        <span class="text-white text-xs font-bold">MM</span>
                                    </div>
                                    <span class="font-semibold text-gray-900">Moov Money</span>
                                    @break
                                @case('bank_transfer')
                                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center shadow-md">
                                        <span class="text-white text-lg">🏦</span>
                                    </div>
                                    <span class="font-semibold text-gray-900">Virement bancaire</span>
                                    @break
                                @case('cash_on_delivery')
                                    <div class="w-8 h-8 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center shadow-md">
                                        <span class="text-white text-lg">💵</span>
                                    </div>
                                    <span class="font-semibold text-gray-900">Paiement à la livraison</span>
                                    @break
                                @default
                                    <span class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                            @endswitch
                        </div>
                    </div>
                    
                    @if($order->payment_phone)
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Numéro de paiement</label>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $order->payment_phone }}</p>
                    </div>
                    @endif
                    
                    @if($order->payment_reference)
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Référence</label>
                        <p class="text-sm font-mono bg-gray-100 px-3 py-2 rounded-lg mt-1 border border-gray-300">
                            {{ $order->payment_reference }}
                        </p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut du paiement</label>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold
                                @switch($order->payment_status)
                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                    @case('paid') bg-green-100 text-green-800 @break
                                    @case('failed') bg-red-100 text-red-800 @break
                                @endswitch">
                                @switch($order->payment_status)
                                    @case('pending') ⏳ En attente @break
                                    @case('paid') ✅ Payé @break
                                    @case('failed') ❌ Échoué @break
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Informations techniques --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-gray-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">⚙️</span> Informations techniques
                </h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">ID Commande</span>
                        <span class="font-mono font-bold text-gray-900">#{{ $order->id }}</span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Créée le</span>
                        <span class="text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Modifiée le</span>
                        <span class="text-gray-900">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 font-medium">Nombre d'articles</span>
                        <span class="font-bold text-blue-600">{{ $order->orderItems->sum('quantity') }}</span>
                    </div>
                </div>
            </div>
            
            {{-- Actions rapides --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-lg p-6 border-t-4 border-indigo-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">⚡</span> Actions rapides
                </h2>
                
                <div class="space-y-3">
                    <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer_phone) }}?text={{ urlencode('Bonjour ' . $order->customer_name . ', votre commande ' . $order->order_number . ' est prête pour la livraison !') }}" 
                       class="w-full inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all"
                       target="_blank">
                        <span class="mr-2">💬</span> Notifier livraison
                    </a>
                    
                    <a href="https://wa.me/{{ str_replace(['+', ' '], '', $order->customer_phone) }}?text={{ urlencode('Bonjour ' . $order->customer_name . ', votre commande ' . $order->order_number . ' a été livrée avec succès. Merci pour votre confiance !') }}" 
                       class="w-full inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all"
                       target="_blank">
                        <span class="mr-2">📦</span> Confirmer livraison
                    </a>
                    
                    <a href="{{ route('admin.orders.print', $order) }}"
                       class="w-full inline-flex items-center justify-center bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all"
                       target="_blank">
                        <span class="mr-2">🖨️</span> Imprimer le reçu
                    </a>
                    
                    <a href="{{ route('admin.orders.pdf', $order) }}"
                       class="w-full inline-flex items-center justify-center bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all"
                       target="_blank">
                        <span class="mr-2">📄</span> Télécharger PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
console.log('✅ Page détails commande {{ $order->order_number }} chargée');

// Confirmation avant changement de statut
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const select = this.querySelector('select');
        const oldValue = select.querySelector('option[selected]')?.value;
        const newValue = select.value;
        
        if (oldValue && oldValue !== newValue) {
            if (!confirm(`Confirmer le changement de statut ?`)) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endpush

@endsection

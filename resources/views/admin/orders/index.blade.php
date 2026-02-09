@extends('admin.layouts.app')

@section('title', 'Gestion des commandes - Jackson Energy International')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-montserrat font-bold text-gray-900">📋 Gestion des commandes</h1>
                <p class="text-gray-600 mt-1">Gérez toutes les commandes de votre boutique Jackson Energy</p>
            </div>
            <div class="flex gap-3">
                <button onclick="exportOrders()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                    <span class="mr-2">📥</span> Exporter
                </button>
            </div>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-blue-100 to-blue-200">
                    <span class="text-2xl">📋</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Total commandes</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $orders->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200">
                    <span class="text-2xl">⏳</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">En attente</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Order::where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-green-100 to-green-200">
                    <span class="text-2xl">✅</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Complétées</p>
                    <p class="text-2xl font-bold text-green-600">{{ \App\Models\Order::where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-emerald-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-emerald-100 to-emerald-200">
                    <span class="text-2xl">💰</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Revenu total</p>
                    <p class="text-xl font-bold text-emerald-600">
                        {{ number_format(\App\Models\Order::where('payment_status', 'paid')->sum('total_amount'), 0, ',', ' ') }} F
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[250px]">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="🔍 Rechercher par numéro, client..."
                            class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        >
                        @if(request('search'))
                            <button
                                type="button"
                                onclick="this.previousElementSibling.value=''; this.closest('form').submit();"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600"
                            >
                                ✕
                            </button>
                        @endif
                    </div>
                </div>

                <select
                    name="status"
                    class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="">📦 Tous les statuts</option>
                    <option value="pending"    {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                    <option value="confirmed"  {{ request('status') == 'confirmed' ? 'selected' : '' }}>✅ Confirmée</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>🔄 En traitement</option>
                    <option value="shipped"    {{ request('status') == 'shipped' ? 'selected' : '' }}>🚚 Expédiée</option>
                    <option value="delivered"  {{ request('status') == 'delivered' ? 'selected' : '' }}>📦 Livrée</option>
                    <option value="completed"  {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Complétée</option>
                    <option value="cancelled"  {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Annulée</option>
                </select>

                <select
                    name="payment_status"
                    class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                >
                    <option value="">💳 Paiement</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>⏳ En attente</option>
                    <option value="paid"    {{ request('payment_status') == 'paid' ? 'selected' : '' }}>✅ Payé</option>
                    <option value="failed"  {{ request('payment_status') == 'failed' ? 'selected' : '' }}>❌ Échoué</option>
                </select>

                <button
                    type="submit"
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow hover:shadow-lg transition-all"
                >
                    Filtrer
                </button>

                @if(request()->hasAny(['search', 'status', 'payment_status']))
                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow hover:shadow-lg transition-all"
                    >
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table des commandes --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Commande</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Produits</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                        <span class="text-xl">📋</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $order->order_number }}</div>
                                        <div class="text-xs text-gray-500 mt-1 flex items-center">
                                            <span class="mr-1">💳</span>
                                            {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $order->customer_name }}</div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center">
                                    <span class="mr-1">📧</span>
                                    {{ Str::limit($order->customer_email, 25) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center">
                                    <span class="mr-1">📱</span>
                                    {{ $order->customer_phone }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    @foreach($order->orderItems->take(2) as $item)
                                        <div class="flex items-center mb-1.5">
                                            <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xs font-bold mr-2">
                                                {{ $item->quantity }}
                                            </span>
                                            <span class="text-gray-900 text-xs">{{ Str::limit($item->product_name, 30) }}</span>
                                        </div>
                                    @endforeach
                                    @if($order->orderItems->count() > 2)
                                        <div class="text-xs text-gray-500 italic mt-1">
                                            +{{ $order->orderItems->count() - 2 }} autre(s) produit(s)
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-base font-bold text-green-600">
                                    {{ number_format($order->total_amount, 0, ',', ' ') }} F
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                        @switch($order->status)
                                            @case('pending')    bg-yellow-100 text-yellow-800 @break
                                            @case('confirmed')  bg-blue-100 text-blue-800 @break
                                            @case('processing') bg-indigo-100 text-indigo-800 @break
                                            @case('shipped')    bg-purple-100 text-purple-800 @break
                                            @case('delivered')  bg-green-100 text-green-800 @break
                                            @case('completed')  bg-emerald-100 text-emerald-800 @break
                                            @case('cancelled')  bg-red-100 text-red-800 @break
                                            @default            bg-gray-100 text-gray-800
                                        @endswitch">
                                        @switch($order->status)
                                            @case('pending')    ⏳ En attente @break
                                            @case('confirmed')  ✅ Confirmée @break
                                            @case('processing') 🔄 En traitement @break
                                            @case('shipped')    🚚 Expédiée @break
                                            @case('delivered')  📦 Livrée @break
                                            @case('completed')  ✅ Complétée @break
                                            @case('cancelled')  ❌ Annulée @break
                                            @default {{ ucfirst($order->status) }}
                                        @endswitch
                                    </span>

                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                        @switch($order->payment_status)
                                            @case('pending') bg-yellow-100 text-yellow-800 @break
                                            @case('paid')    bg-green-100 text-green-800 @break
                                            @case('failed')  bg-red-100 text-red-800 @break
                                            @default         bg-gray-100 text-gray-800
                                        @endswitch">
                                        @switch($order->payment_status)
                                            @case('pending') 💳 En attente @break
                                            @case('paid')    ✅ Payé @break
                                            @case('failed')  ❌ Échoué @break
                                            @default {{ ucfirst($order->payment_status) }}
                                        @endswitch
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">
                                    {{ $order->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $order->created_at->format('H:i') }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110"
                                        title="Voir les détails"
                                    >
                                        <span class="text-xl">👁️</span>
                                    </a>

                                    {{-- Nouveau : bouton pour imprimer le reçu --}}
                                    <a
                                        href="{{ route('admin.orders.receipt', $order) }}"
                                        class="text-purple-600 hover:text-purple-800 transition transform hover:scale-110"
                                        title="Imprimer le reçu"
                                        target="_blank"
                                    >
                                        <span class="text-xl">🧾</span>
                                    </a>

                                    <button
                                        type="button"
                                        onclick="confirmDeleteOrder({{ $order->id }}, '{{ addslashes($order->order_number) }}')"
                                        class="text-red-600 hover:text-red-800 transition transform hover:scale-110"
                                        title="Supprimer la commande"
                                    >
                                        <span class="text-xl">🗑️</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-7xl opacity-20 mb-4">📋</span>
                                    <p class="text-xl text-gray-500 font-semibold mb-2">Aucune commande trouvée</p>
                                    <p class="text-sm text-gray-400 mb-4">Les commandes apparaîtront ici</p>
                                    @if(request()->hasAny(['search', 'status', 'payment_status']))
                                        <a
                                            href="{{ route('admin.orders.index') }}"
                                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition"
                                        >
                                            Réinitialiser les filtres
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif

    {{-- Modal de suppression --}}
    <div
        id="deleteOrderModal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4"
    >
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex justify-center mb-4">
                    <div class="flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                        <span class="text-red-600 text-3xl">⚠️</span>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Confirmer la suppression</h3>
                    <p class="text-sm text-gray-600 mb-2">
                        Êtes-vous sûr de vouloir supprimer la commande :
                    </p>
                    <p class="text-base font-semibold text-gray-900 mb-4">
                        "<span id="orderNumber"></span>" ?
                    </p>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                        <p class="text-xs text-red-800">
                            ⚠️ Cette action est irréversible et supprimera également tous les éléments de cette commande.
                        </p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button
                        type="button"
                        onclick="closeDeleteOrderModal()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition"
                    >
                        Annuler
                    </button>
                    <form id="deleteOrderForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition"
                        >
                            Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDeleteOrder(orderId, orderNumber) {
    document.getElementById('orderNumber').textContent = orderNumber;
    document.getElementById('deleteOrderForm').action = `/admin/orders/${orderId}`;
    document.getElementById('deleteOrderModal').classList.remove('hidden');
}

function closeDeleteOrderModal() {
    document.getElementById('deleteOrderModal').classList.add('hidden');
}

// Fermer modal en cliquant à l'extérieur
document.getElementById('deleteOrderModal')?.addEventListener('click', function (e) {
    if (e.target === this) {
        closeDeleteOrderModal();
    }
});

// Export des commandes (placeholder)
function exportOrders() {
    if (window.showAdminNotification) {
        window.showAdminNotification("Fonctionnalité d'export en développement", 'info');
    } else {
        alert('ℹ️ Fonctionnalité d\'export en développement');
    }
}

console.log('✅ Page gestion commandes initialisée');
</script>
@endpush

@endsection

@extends('admin.layouts.app')

@section('title', 'Gestion des commandes - Administration')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-montserrat font-bold text-gray-900">Gestion des commandes</h1>
    <p class="text-gray-600">Gérez toutes les commandes de votre boutique</p>
</div>

{{-- Table des commandes --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commande</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produits</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                            <div class="text-sm text-gray-500">{{ $order->payment_method }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->customer_name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->customer_email }}</div>
                            <div class="text-sm text-gray-500">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                @foreach($order->orderItems as $item)
                                    <div class="mb-1">
                                        {{ $item->product_name }} ({{ $item->quantity }}x)
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            <div class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                {{-- Voir la commande --}}
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Voir la commande">
                                    👁️
                                </a>

                                {{-- ✅ NOUVEAU : Bouton supprimer --}}
                                <button type="button"
                                        onclick="confirmDeleteOrder({{ $order->id }}, '{{ $order->order_number }}')"
                                        class="text-red-600 hover:text-red-900 transition"
                                        title="Supprimer la commande">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <p class="text-xl mb-2">📋</p>
                                <p>Aucune commande trouvée</p>
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

{{-- ✅ NOUVEAU : Modal de confirmation de suppression des commandes --}}
<div id="deleteOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <span class="text-red-600 text-xl">⚠️</span>
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Confirmer la suppression</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Êtes-vous sûr de vouloir supprimer la commande "<span id="orderNumber" class="font-semibold"></span>" ?
                        Cette action supprimera également tous les éléments de cette commande et est irréversible.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button"
                            onclick="closeDeleteOrderModal()"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition">
                        Annuler
                    </button>
                    <form id="deleteOrderForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                            Supprimer
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

// Fermer le modal en cliquant en dehors
document.getElementById('deleteOrderModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteOrderModal();
    }
});
</script>
@endpush

@endsection

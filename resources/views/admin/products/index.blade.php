@extends('admin.layouts.app')

@section('title', 'Gestion des produits - Administration')

@section('content')
{{-- Header avec boutons d'action --}}
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-montserrat font-bold text-gray-900">Gestion des produits</h1>
            <p class="text-gray-600">Gérez votre catalogue de produits</p>
        </div>
        <div class="flex space-x-3">
            {{-- ✅ NOUVEAU : Bouton pour créer une campagne publicitaire --}}
            <button id="bulkAdvertiseBtn"
                    onclick="openAdvertiseModal()"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                    disabled>
                📢 Faire de la publicité
            </button>
            <a href="{{ route('admin.products.create') }}"
               class="bg-vert-energie text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                ➕ Ajouter un produit
            </a>
        </div>
    </div>
</div>

{{-- Statistiques rapides --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                📦
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total produits</p>
                <p class="text-2xl font-bold text-gray-900">{{ $products->total() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                ✅
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Produits actifs</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Product::where('is_active', true)->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                ⭐
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Produits vedettes</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Product::where('is_featured', true)->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                ⚠️
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Stock faible</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Product::where('stock_quantity', '<', 10)->count() }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ✅ NOUVEAU : Section de sélection multiple pour publicités --}}
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <label class="flex items-center">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600">
                    <span class="ml-2 text-sm text-gray-700">Sélectionner tout</span>
                </label>
                <span id="selectedCount" class="text-sm text-gray-500">0 produit(s) sélectionné(s)</span>
            </div>
            <div class="flex space-x-2">
                <button onclick="openAdvertiseModal()"
                        id="selectedAdvertiseBtn"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        disabled>
                    📢 Publicité pour sélection
                </button>
                <a href="{{ route('admin.ad-campaigns.index') }}"
                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                    📊 Voir les campagnes
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Filtres et recherche --}}
<div class="bg-white rounded-lg shadow mb-6">
    <div class="p-6">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Rechercher un produit..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
            </div>
            <div>
                <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                    <option value="">Toutes les catégories</option>
                    @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>Vedettes</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                🔍 Rechercher
            </button>
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.products.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    🗑️ Effacer
                </a>
            @endif
        </form>
    </div>
</div>

{{-- Table des produits --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    {{-- ✅ NOUVEAU : Colonne de sélection --}}
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-blue-600">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        {{-- ✅ NOUVEAU : Checkbox de sélection --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="selected_products[]" value="{{ $product->id }}"
                                   class="product-checkbox rounded border-gray-300 text-blue-600"
                                   onchange="updateSelection()"
                                   data-name="{{ $product->name }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-16 w-16">
                                    <img class="h-16 w-16 rounded-lg object-cover"
                                         src="{{ $product->first_image }}"
                                         alt="{{ $product->name }}">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                    @if($product->is_featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                            ⭐ Vedette
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($product->promotional_price)
                                    <div class="text-green-600 font-semibold">{{ number_format($product->promotional_price, 0, ',', ' ') }} FCFA</div>
                                    <div class="text-gray-400 line-through text-xs">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
                                @else
                                    <div class="font-semibold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($product->stock_quantity > 10)
                                    <span class="text-green-600 font-semibold">{{ $product->stock_quantity }}</span>
                                @elseif($product->stock_quantity > 0)
                                    <span class="text-yellow-600 font-semibold">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="text-red-600 font-semibold">0</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Actif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ❌ Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                {{-- ✅ NOUVEAU : Bouton publicité individuel --}}
                                <a href="{{ route('admin.ad-campaigns.create', ['products' => [$product->id]]) }}"
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Faire de la publicité">
                                    📢
                                </a>

                                {{-- Voir le produit --}}
                                <a href="{{ route('products.show', $product->slug) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Voir le produit">
                                    👁️
                                </a>

                                {{-- Modifier --}}
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="text-indigo-600 hover:text-indigo-900 transition"
                                   title="Modifier">
                                    ✏️
                                </a>

                                {{-- Supprimer --}}
                                <button type="button"
                                        onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')"
                                        class="text-red-600 hover:text-red-900 transition"
                                        title="Supprimer">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <p class="text-xl mb-2">📦</p>
                                <p>Aucun produit trouvé</p>
                                @if(request()->hasAny(['search', 'category', 'status']))
                                    <a href="{{ route('admin.products.index') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                        Voir tous les produits
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
@if($products->hasPages())
    <div class="mt-6">
        {{ $products->links() }}
    </div>
@endif

{{-- Modal de confirmation de suppression --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
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
                        Êtes-vous sûr de vouloir supprimer le produit "<span id="productName" class="font-semibold"></span>" ?
                        Cette action est irréversible.
                    </p>
                </div>
                <div class="flex space-x-3">
                    <button type="button"
                            onclick="closeDeleteModal()"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition">
                        Annuler
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
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

{{-- ✅ NOUVEAU : Modal pour créer une campagne publicitaire --}}
<div id="advertiseModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Créer une campagne publicitaire</h3>
                    <button onclick="closeAdvertiseModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>

                <form id="campaignForm" method="GET" action="{{ route('admin.ad-campaigns.create') }}">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Produits sélectionnés</label>
                            <div id="selectedProductsList" class="bg-gray-50 p-4 rounded-lg min-h-20">
                                <p class="text-gray-500 text-sm">Aucun produit sélectionné</p>
                            </div>
                            <input type="hidden" name="products" id="selectedProductsInput">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Budget estimé (FCFA)</label>
                                <select name="budget" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="10000">10 000 FCFA</option>
                                    <option value="25000" selected>25 000 FCFA</option>
                                    <option value="50000">50 000 FCFA</option>
                                    <option value="100000">100 000 FCFA</option>
                                    <option value="250000">250 000 FCFA</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Durée (jours)</label>
                                <select name="duration" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <option value="7">7 jours</option>
                                    <option value="14" selected>14 jours</option>
                                    <option value="30">30 jours</option>
                                    <option value="60">60 jours</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Plateformes</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="platform" value="both" checked class="mr-3 text-blue-600">
                                    <div>
                                        <div class="font-medium">Google Ads + Meta Ads</div>
                                        <div class="text-sm text-gray-500">Portée maximale - Recommandé</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="platform" value="google_ads" class="mr-3 text-blue-600">
                                    <div>
                                        <div class="font-medium">Google Ads uniquement</div>
                                        <div class="text-sm text-gray-500">Recherche Google et sites partenaires</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="platform" value="meta_ads" class="mr-3 text-blue-600">
                                    <div>
                                        <div class="font-medium">Meta Ads uniquement</div>
                                        <div class="text-sm text-gray-500">Facebook et Instagram</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-8">
                        <button type="button" onclick="closeAdvertiseModal()"
                                class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                            Annuler
                        </button>
                        <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Créer la campagne
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedProducts = [];

// Gestion de la sélection multiple
function updateSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    selectedProducts = Array.from(checkboxes).map(cb => ({
        id: cb.value,
        name: cb.getAttribute('data-name')
    }));

    document.getElementById('selectedCount').textContent = `${selectedProducts.length} produit(s) sélectionné(s)`;

    // Activer/désactiver les boutons
    const hasSelection = selectedProducts.length > 0;
    document.getElementById('bulkAdvertiseBtn').disabled = !hasSelection;
    document.getElementById('selectedAdvertiseBtn').disabled = !hasSelection;

    // Mettre à jour la case "Sélectionner tout"
    const totalCheckboxes = document.querySelectorAll('.product-checkbox').length;
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');

    if (selectedProducts.length === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
        selectAllHeaderCheckbox.indeterminate = false;
        selectAllHeaderCheckbox.checked = false;
    } else if (selectedProducts.length === totalCheckboxes) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = true;
        selectAllHeaderCheckbox.indeterminate = false;
        selectAllHeaderCheckbox.checked = true;
    } else {
        selectAllCheckbox.indeterminate = true;
        selectAllHeaderCheckbox.indeterminate = true;
    }
}

// Sélectionner tout
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelection();
});

document.getElementById('selectAllHeader').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    document.getElementById('selectAll').checked = this.checked;
    updateSelection();
});

// Modal de publicité
function openAdvertiseModal() {
    if (selectedProducts.length === 0) {
        alert('Veuillez sélectionner au moins un produit');
        return;
    }

    // Remplir la liste des produits sélectionnés
    const productsList = document.getElementById('selectedProductsList');
    productsList.innerHTML = selectedProducts.map(product =>
        `<div class="flex items-center justify-between bg-white p-3 rounded mb-2 border">
            <span class="text-sm font-medium">${product.name}</span>
            <button type="button" onclick="removeProduct('${product.id}')" class="text-red-500 hover:text-red-700 font-bold">×</button>
        </div>`
    ).join('');

    // Remplir l'input caché
    document.getElementById('selectedProductsInput').value = selectedProducts.map(p => p.id).join(',');

    document.getElementById('advertiseModal').classList.remove('hidden');
}

function closeAdvertiseModal() {
    document.getElementById('advertiseModal').classList.add('hidden');
}

function removeProduct(productId) {
    selectedProducts = selectedProducts.filter(p => p.id !== productId);
    document.querySelector(`input[value="${productId}"]`).checked = false;
    updateSelection();

    if (selectedProducts.length === 0) {
        closeAdvertiseModal();
    } else {
        openAdvertiseModal();
    }
}

// Modal de suppression (fonction existante)
function confirmDelete(productId, productName) {
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteForm').action = `/admin/products/${productId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Fermer les modals en cliquant en dehors
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

document.getElementById('advertiseModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAdvertiseModal();
    }
});

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateSelection();
});
</script>
@endpush

@endsection

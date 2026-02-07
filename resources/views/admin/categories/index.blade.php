@extends('admin.layouts.app')

@section('title', 'Gestion des catégories - Jackson Energy International')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Messages de feedback --}}
    @if(session('success'))
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md animate-fade-in">
        <div class="flex items-center">
            <span class="text-2xl mr-3">✅</span>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
        <div class="flex items-center">
            <span class="text-2xl mr-3">⚠️</span>
            <p class="font-semibold">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-montserrat font-bold text-gray-900">📁 Gestion des catégories</h1>
                <p class="text-gray-600 mt-1">Organisez vos produits par catégories</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                <span class="mr-2">➕</span> Ajouter une catégorie
            </a>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-blue-100 to-blue-200">
                    <span class="text-2xl">📁</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Total catégories</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $categories->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-green-100 to-green-200">
                    <span class="text-2xl">✅</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Actives</p>
                    <p class="text-2xl font-bold text-green-600">{{ \App\Models\Category::where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-purple-100 to-purple-200">
                    <span class="text-2xl">📦</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Total produits</p>
                    <p class="text-2xl font-bold text-purple-600">{{ \App\Models\Product::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200">
                    <span class="text-2xl">📸</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Avec images</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Category::whereNotNull('image')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table des catégories --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Produits</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Ordre</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categories as $category)
                    <tr class="hover:bg-green-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-14 w-14">
                                    @if($category->image)
                                        <img class="h-14 w-14 rounded-lg object-cover border-2 border-gray-200 shadow-sm" 
                                             src="{{ $category->image }}" 
                                             alt="{{ $category->name }}">
                                    @else
                                        <div class="h-14 w-14 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-2 border-gray-300">
                                            <span class="text-2xl">📁</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $category->name }}</div>
                                    @if($category->description)
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($category->description, 60) }}</div>
                                    @endif
                                    @if($category->icon)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                {!! $category->icon !!}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                               class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                   {{ $category->products_count > 0 ? 'bg-purple-100 text-purple-800 hover:bg-purple-200' : 'bg-gray-100 text-gray-600' }} 
                                   transition-colors">
                                📦 {{ $category->products_count }} produit{{ $category->products_count > 1 ? 's' : '' }}
                            </a>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                #{{ $category->sort_order ?? 0 }}
                            </span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    ✅ Actif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                    ❌ Inactif
                                </span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-3">
                                {{-- Voir --}}
                                <a href="{{ route('categories.show', $category->slug) }}" 
                                   class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110"
                                   title="Voir sur le site"
                                   target="_blank">
                                    <span class="text-xl">👁️</span>
                                </a>

                                {{-- Éditer --}}
                                <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-800 transition transform hover:scale-110"
                                   title="Modifier">
                                    <span class="text-xl">✏️</span>
                                </a>

                                {{-- Supprimer --}}
                                @if($category->products_count == 0)
                                    <button type="button"
                                            onclick="confirmDelete({{ $category->id }}, '{{ addslashes($category->name) }}')"
                                            class="text-red-600 hover:text-red-800 transition transform hover:scale-110"
                                            title="Supprimer">
                                        <span class="text-xl">🗑️</span>
                                    </button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed opacity-50" 
                                          title="Impossible de supprimer : {{ $category->products_count }} produit(s) associé(s)">
                                        <span class="text-xl">🗑️</span>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <span class="text-7xl opacity-20 mb-4">📁</span>
                                <p class="text-xl text-gray-500 font-semibold mb-2">Aucune catégorie trouvée</p>
                                <p class="text-sm text-gray-400 mb-4">Commencez par créer votre première catégorie</p>
                                <a href="{{ route('admin.categories.create') }}" 
                                   class="inline-flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all">
                                    <span class="mr-2">➕</span> Créer une catégorie
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modal de suppression --}}
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
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
                    Êtes-vous sûr de vouloir supprimer la catégorie :
                </p>
                <p class="text-base font-semibold text-gray-900 mb-4">
                    "<span id="categoryName"></span>" ?
                </p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                    <p class="text-xs text-yellow-800">
                        ⚠️ Cette action est irréversible.
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition">
                    Annuler
                </button>
                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition">
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Modal de suppression
function confirmDelete(categoryId, categoryName) {
    document.getElementById('categoryName').textContent = categoryName;
    document.getElementById('deleteForm').action = `/admin/categories/${categoryId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Fermer modal en cliquant à l'extérieur
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Fermer avec la touche Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Animation pour les messages de feedback
document.addEventListener('DOMContentLoaded', function() {
    const successMessages = document.querySelectorAll('.animate-fade-in');
    successMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s ease-out';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        }, 5000);
    });

    console.log('✅ Page gestion catégories initialisée');
});
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endpush

@endsection

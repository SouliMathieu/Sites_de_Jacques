@extends('admin.layouts.app')

@section('title', 'Gestion des catégories - Administration')

@section('content')
<div class="admin-categories-burkina">
    <!-- Messages de session -->
    @if(session('success'))
    <div class="admin-message-success mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="admin-message-error mb-6">
        {{ session('error') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-montserrat font-bold text-gray-900">Gestion des catégories</h1>
            <p class="text-gray-600">Organisez vos produits par catégories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="admin-btn-bf">
            ➕ Ajouter une catégorie
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="admin-table-wrapper">
            <table class="admin-table-bf">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Produits</th>
                        <th>Ordre</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    @if($category->image)
                                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ $category->image }}" alt="{{ $category->name }}">
                                    @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">📁</span>
                                    </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-cat-actif">
                                {{ $category->products_count }} produit(s)
                            </span>
                        </td>
                        <td class="text-sm text-gray-900">
                            {{ $category->sort_order }}
                        </td>
                        <td>
                            @if($category->is_active)
                            <span class="badge-cat-actif">Actif</span>
                            @else
                            <span class="badge-cat-inactif">Inactif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center space-x-1">
                                <!-- Voir la catégorie -->
                                <a href="{{ route('admin.categories.show', $category->id) }}" 
                                   class="btn-table-bf inline-flex items-center justify-center w-8 h-8 rounded" 
                                   title="Voir les détails">
                                    👁️
                                </a>
                                
                                <!-- Éditer la catégorie -->
                                <a href="{{ route('admin.categories.edit', $category->id) }}" 
                                   class="btn-table-bf inline-flex items-center justify-center w-8 h-8 rounded" 
                                   title="Modifier">
                                    ✏️
                                </a>
                                
                                <!-- Supprimer la catégorie -->
                                @if($category->products_count == 0)
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                      method="POST" 
                                      class="inline-block"
                                      onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer la catégorie « {{ $category->name }} » ?\n\nCette action est irréversible.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn-table-bf inline-flex items-center justify-center w-8 h-8 rounded"
                                            style="background-color: #ef2b2d; color: white;"
                                            title="Supprimer définitivement"
                                            onmouseover="this.style.backgroundColor='#c32122'" 
                                            onmouseout="this.style.backgroundColor='#ef2b2d'">
                                        🗑️
                                    </button>
                                </form>
                                @else
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded bg-gray-100 text-gray-400 cursor-not-allowed" 
                                      title="Impossible de supprimer : cette catégorie contient {{ $category->products_count }} produit(s)">
                                    🗑️
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            <div class="py-8">
                                <div class="text-4xl mb-4">📁</div>
                                <p class="text-lg font-medium">Aucune catégorie trouvée</p>
                                <p class="text-sm">Commencez par créer votre première catégorie pour organiser vos produits</p>
                                <a href="{{ route('admin.categories.create') }}" class="admin-btn-bf mt-4 inline-block">
                                    ➕ Ajouter une catégorie
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>

<!-- JavaScript pour améliorer l'UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Améliorer la confirmation de suppression
    const deleteButtons = document.querySelectorAll('form[onsubmit*="confirm"] button[type="submit"]');
    deleteButtons.forEach(button => {
        const form = button.closest('form');
        form.addEventListener('submit', function(e) {
            // Le onsubmit du form gère déjà la confirmation
            // Ici on peut ajouter un loader ou d'autres effets visuels
            if (confirm) {
                button.innerHTML = '⏳';
                button.disabled = true;
            }
        });
    });
});
</script>
@endsection

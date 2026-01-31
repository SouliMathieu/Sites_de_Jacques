@extends('admin.layouts.app')

@section('title', 'Gestion des catégories - Administration')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-montserrat font-bold text-gray-900">Gestion des catégories</h1>
        <p class="text-gray-600">Organisez vos produits par catégories</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="bg-vert-energie text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-medium">
        ➕ Ajouter une catégorie
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catégorie</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produits</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
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
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            {{ $category->products_count }} produit(s)
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $category->sort_order }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($category->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Actif</span>
                        @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">Inactif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.categories.show', $category) }}" class="text-blue-600 hover:text-blue-900">👁️</a>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-green-600 hover:text-green-900">✏️</a>
                            @if($category->products_count == 0)
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">🗑️</button>
                            </form>
                            @else
                            <span class="text-gray-400" title="Impossible de supprimer une catégorie contenant des produits">🗑️</span>
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
                            <p class="text-sm">Commencez par créer votre première catégorie</p>
                            <a href="{{ route('admin.categories.create') }}" class="mt-4 inline-block bg-vert-energie text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                                ➕ Ajouter une catégorie
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($categories->hasPages())
    <div class="px-6 py-4 border-t">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection

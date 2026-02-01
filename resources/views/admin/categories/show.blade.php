@extends('admin.layouts.app')

@section('title', 'Détails de la catégorie - Administration')

@section('content')
<div class="admin-categories-burkina">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-montserrat font-bold text-gray-900">{{ $category->name }}</h1>
            <p class="text-gray-600">Détails de la catégorie</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="admin-btn-bf">
                ✏️ Modifier
            </a>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                ← Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informations de la catégorie -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Informations générales</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $category->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Slug</label>
                        <p class="mt-1 text-sm text-gray-500">{{ $category->slug }}</p>
                    </div>

                    @if($category->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $category->description }}</p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $category->sort_order }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <p class="mt-1">
                            @if($category->is_active)
                            <span class="badge-cat-actif">Actif</span>
                            @else
                            <span class="badge-cat-inactif">Inactif</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre de produits</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $category->products->count() }} produit(s)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image de la catégorie -->
        <div>
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Image</h2>
                
                @if($category->image)
                <div class="aspect-square rounded-lg overflow-hidden">
                    <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                </div>
                @else
                <div class="aspect-square rounded-lg bg-gray-100 flex items-center justify-center">
                    <span class="text-6xl text-gray-400">📁</span>
                </div>
                <p class="mt-2 text-sm text-gray-500 text-center">Aucune image</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Produits de la catégorie -->
    @if($category->products->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Produits récents ({{ $category->products->count() }} sur {{ $category->products()->count() }})</h2>
            </div>
            <div class="p-6">
                <div class="admin-table-wrapper">
                    <table class="admin-table-bf">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $product)
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        @if($product->first_image)
                                        <img class="h-10 w-10 rounded-lg object-cover mr-3" src="{{ $product->first_image }}" alt="{{ $product->name }}">
                                        @else
                                        <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                            <span class="text-gray-500">📦</span>
                                        </div>
                                        @endif
                                        <span class="font-medium">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td>{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</td>
                                <td>{{ $product->stock_quantity }}</td>
                                <td>
                                    @if($product->is_active)
                                    <span class="badge-cat-actif">Actif</span>
                                    @else
                                    <span class="badge-cat-inactif">Inactif</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn-table-bf" title="Voir le produit">
                                        👁️
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($category->products()->count() > 10)
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" class="admin-btn-bf">
                        Voir tous les produits ({{ $category->products()->count() }})
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Catégorie : ' . $category->name . ' - Jackson Energy International')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-montserrat font-bold text-gray-900">
                        @if($category->icon)
                            {!! $category->icon !!}
                        @else
                            📁
                        @endif
                        {{ $category->name }}
                    </h1>
                    <span class="px-4 py-1.5 rounded-full text-sm font-bold
                        {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $category->is_active ? '✅ Actif' : '❌ Inactif' }}
                    </span>
                </div>
                <p class="text-gray-600">
                    Créée le {{ $category->created_at->format('d/m/Y à H:i') }} • 
                    {{ $category->products_count ?? $category->products->count() }} produit(s)
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('categories.show', $category->slug) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all"
                   target="_blank">
                    <span class="mr-2">👁️</span> Voir sur le site
                </a>
                <a href="{{ route('admin.categories.edit', $category->id) }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                    <span class="mr-2">✏️</span> Modifier
                </a>
                <a href="{{ route('admin.categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold rounded-lg shadow-md hover:shadow-lg transition-all">
                    ← Retour
                </a>
            </div>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-purple-100 to-purple-200">
                    <span class="text-2xl">📦</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Produits</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $category->products->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-green-100 to-green-200">
                    <span class="text-2xl">✅</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Actifs</p>
                    <p class="text-2xl font-bold text-green-600">{{ $category->products->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200">
                    <span class="text-2xl">⭐</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Vedettes</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $category->products->where('is_featured', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-red-100 to-red-200">
                    <span class="text-2xl">⚠️</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Stock bas</p>
                    <p class="text-2xl font-bold text-red-600">{{ $category->products->where('stock_quantity', '<', 10)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Informations principales --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Informations générales --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">ℹ️</span>
                    Informations générales
                </h2>
                
                <div class="space-y-4">
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom de la catégorie</label>
                        <p class="mt-1 text-lg font-bold text-gray-900">{{ $category->name }}</p>
                    </div>

                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Slug (URL)</label>
                        <div class="mt-1 flex items-center gap-2">
                            <p class="text-sm font-mono text-gray-600 bg-gray-100 px-3 py-1 rounded-lg">{{ $category->slug }}</p>
                            <button onclick="copyToClipboard('{{ route('categories.show', $category->slug) }}')" 
                                    class="text-blue-600 hover:text-blue-800 transition"
                                    title="Copier le lien">
                                📋
                            </button>
                        </div>
                    </div>

                    @if($category->description)
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</label>
                        <p class="mt-2 text-gray-900 leading-relaxed">{{ $category->description }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Ordre d'affichage</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
                                    #{{ $category->sort_order ?? 0 }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nombre de produits</label>
                            <p class="mt-1">
                                <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                                   class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-purple-100 text-purple-800 hover:bg-purple-200 transition">
                                    📦 {{ $category->products->count() }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Métadonnées SEO --}}
            @if($category->meta_title || $category->meta_description)
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">🔍</span>
                    Optimisation SEO
                </h2>
                
                <div class="space-y-4">
                    @if($category->meta_title)
                    <div class="pb-4 border-b border-gray-200">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Titre SEO</label>
                        <p class="mt-1 text-gray-900">{{ $category->meta_title }}</p>
                    </div>
                    @endif

                    @if($category->meta_description)
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Description SEO</label>
                        <p class="mt-1 text-gray-900">{{ $category->meta_description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Informations système --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-gray-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gray-100 text-gray-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">⚙️</span>
                    Informations système
                </h2>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">ID Catégorie</span>
                        <p class="font-mono font-bold text-gray-900 mt-1">#{{ $category->id }}</p>
                    </div>
                    <div class="pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Statut</span>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $category->is_active ? '✅ Actif' : '❌ Inactif' }}
                            </span>
                        </p>
                    </div>
                    <div class="pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Date de création</span>
                        <p class="text-gray-900 mt-1">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="pb-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Dernière modification</span>
                        <p class="text-gray-900 mt-1">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-600 font-medium">Il y a</span>
                        <p class="text-gray-900 mt-1">{{ $category->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="space-y-6">
            {{-- Image de la catégorie --}}
            <div class="bg-white rounded-lg shadow-lg p-6 border-t-4 border-purple-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">📸</span> Image
                </h2>
                
                @if($category->image)
                    <div class="relative aspect-square rounded-lg overflow-hidden shadow-md border-2 border-gray-200 group">
                        <img src="{{ $category->image }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                            <p class="text-white text-xs font-semibold">{{ $category->name }}</p>
                        </div>
                    </div>
                @else
                    <div class="aspect-square rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-2 border-dashed border-gray-400">
                        <div class="text-center">
                            <span class="text-6xl text-gray-400 block mb-2">📁</span>
                            <p class="text-sm text-gray-500 font-medium">Aucune image</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Actions rapides --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-lg p-6 border-t-4 border-indigo-500">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">⚡</span> Actions rapides
                </h2>
                
                <div class="space-y-3">
                    <a href="{{ route('categories.show', $category->slug) }}" 
                       class="w-full inline-flex items-center justify-center bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all"
                       target="_blank">
                        <span class="mr-2">👁️</span> Voir sur le site
                    </a>
                    
                    <a href="{{ route('admin.categories.edit', $category->id) }}" 
                       class="w-full inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                        <span class="mr-2">✏️</span> Modifier la catégorie
                    </a>
                    
                    <a href="{{ route('admin.products.create', ['category' => $category->id]) }}" 
                       class="w-full inline-flex items-center justify-center bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                        <span class="mr-2">➕</span> Ajouter un produit
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Produits de la catégorie --}}
    @if($category->products->count() > 0)
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-indigo-50 border-b border-purple-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <span class="bg-purple-100 text-purple-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">📦</span>
                        Produits de la catégorie ({{ $category->products->count() }})
                    </h2>
                    @if($category->products->count() > 0)
                    <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                       class="text-sm font-semibold text-purple-600 hover:text-purple-800 transition">
                        Voir tous →
                    </a>
                    @endif
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase">Produit</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase">Prix</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Stock</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($category->products->take(10) as $product)
                        <tr class="hover:bg-purple-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if($product->hasImages())
                                            <img class="h-12 w-12 rounded-lg object-cover border-2 border-gray-200 shadow-sm" 
                                                 src="{{ $product->first_image }}" 
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                <span class="text-xl">📦</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ Str::limit($product->name, 40) }}</div>
                                        @if($product->is_featured)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 mt-1">
                                                ⭐ Vedette
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ number_format($product->price, 0, ',', ' ') }} F
                                </div>
                                @if($product->promotional_price)
                                    <div class="text-xs text-gray-500 line-through">
                                        {{ number_format($product->price, 0, ',', ' ') }} F
                                    </div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    {{ $product->stock_quantity > 10 ? 'bg-green-100 text-green-800' : ($product->stock_quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->is_active ? '✅ Actif' : '❌ Inactif' }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110"
                                       title="Voir sur le site"
                                       target="_blank">
                                        <span class="text-xl">👁️</span>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                       class="text-indigo-600 hover:text-indigo-800 transition transform hover:scale-110"
                                       title="Modifier">
                                        <span class="text-xl">✏️</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($category->products->count() > 10)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 text-center">
                <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all">
                    Voir tous les produits ({{ $category->products->count() }}) →
                </a>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <span class="text-7xl opacity-20 block mb-4">📦</span>
            <p class="text-xl text-gray-500 font-semibold mb-2">Aucun produit dans cette catégorie</p>
            <p class="text-sm text-gray-400 mb-6">Commencez par ajouter des produits</p>
            <a href="{{ route('admin.products.create', ['category' => $category->id]) }}" 
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all">
                <span class="mr-2">➕</span> Ajouter un produit
            </a>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        if (window.showAdminNotification) {
            window.showAdminNotification('Lien copié !', 'success');
        } else {
            alert('✅ Lien copié dans le presse-papiers !');
        }
    }).catch(err => {
        console.error('Erreur de copie:', err);
    });
}

console.log('✅ Page détails catégorie "{{ $category->name }}" chargée');
</script>
@endpush

@endsection

@extends('admin.layouts.app')

@section('title', 'Gestion des produits - Jackson Energy International')

@section('content')
<div class="max-w-7xl mx-auto">
    {{-- Header avec actions --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-montserrat font-bold text-gray-900">📦 Gestion des produits</h1>
                <p class="text-gray-600 mt-1">Gérez votre catalogue Jackson Energy</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button id="bulkAdvertiseBtn"
                        onclick="openAdvertiseModal()"
                        class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        disabled>
                    <span class="mr-2">📢</span> Faire de la publicité
                </button>
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                    <span class="mr-2">➕</span> Ajouter un produit
                </a>
            </div>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-blue-100 to-blue-200">
                    <span class="text-2xl">📦</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Total</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $products->total() }}</p>
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
                    <p class="text-2xl font-bold text-green-600">{{ \App\Models\Product::where('is_active', true)->count() }}</p>
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
                    <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Product::where('is_featured', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-purple-100 to-purple-200">
                    <span class="text-2xl">📸</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Avec médias</p>
                    <p class="text-2xl font-bold text-purple-600">{{ \App\Models\Product::withImages()->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-5 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gradient-to-br from-red-100 to-red-200">
                    <span class="text-2xl">⚠️</span>
                </div>
                <div class="ml-4">
                    <p class="text-xs font-medium text-gray-600 uppercase">Stock faible</p>
                    <p class="text-2xl font-bold text-red-600">{{ \App\Models\Product::where('stock_quantity', '<', 10)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Barre de sélection multiple --}}
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-md mb-6 border border-blue-200">
        <div class="p-4">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 w-5 h-5 focus:ring-2 focus:ring-blue-500">
                        <span class="ml-2 text-sm font-semibold text-gray-700 group-hover:text-blue-600 transition">Tout sélectionner</span>
                    </label>
                    <div class="h-6 w-px bg-gray-300"></div>
                    <span id="selectedCount" class="text-sm font-medium text-gray-600 bg-white px-3 py-1 rounded-full shadow-sm">
                        0 sélectionné(s)
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button onclick="openAdvertiseModal()"
                            id="selectedAdvertiseBtn"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        <span class="mr-1.5">📢</span> Créer campagne
                    </button>
                    <a href="{{ route('admin.ad-campaigns.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg shadow hover:shadow-lg transition-all">
                        <span class="mr-1.5">📊</span> Mes campagnes
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[250px]">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="🔍 Rechercher un produit..."
                               class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        @if(request('search'))
                            <button type="button" onclick="this.previousElementSibling.value=''; this.closest('form').submit();"
                                    class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                ✕
                            </button>
                        @endif
                    </div>
                </div>
                
                <select name="category" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">📁 Toutes les catégories</option>
                    @foreach(\App\Models\Category::orderBy('name')->get() as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                
                <select name="status" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-white">
                    <option value="">🔄 Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Actifs</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>❌ Inactifs</option>
                    <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>⭐ Vedettes</option>
                    <option value="with_images" {{ request('status') == 'with_images' ? 'selected' : '' }}>📸 Avec images</option>
                    <option value="with_videos" {{ request('status') == 'with_videos' ? 'selected' : '' }}>🎥 Avec vidéos</option>
                </select>
                
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow hover:shadow-lg transition-all">
                    Filtrer
                </button>
                
                @if(request()->hasAny(['search', 'category', 'status']))
                    <a href="{{ route('admin.products.index') }}" 
                       class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow hover:shadow-lg transition-all">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>
    </div>

    {{-- Table des produits --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" id="selectAllHeader" 
                                   class="rounded border-gray-300 text-blue-600 w-5 h-5 focus:ring-2 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Prix</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Médias</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-4 py-4">
                                <input type="checkbox" name="selected_products[]" value="{{ $product->id }}"
                                       class="product-checkbox rounded border-gray-300 text-blue-600 w-5 h-5 focus:ring-2 focus:ring-blue-500"
                                       onchange="updateSelection()"
                                       data-name="{{ $product->name }}">
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16">
                                        @if($product->hasImages())
                                            <img class="h-16 w-16 rounded-lg object-cover border-2 border-gray-200 shadow-sm"
                                                 src="{{ $product->first_image }}"
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="h-16 w-16 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center border-2 border-gray-300">
                                                <span class="text-gray-500 text-2xl">📷</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ Str::limit($product->name, 40) }}</div>
                                        <div class="text-xs text-gray-500 mt-1">{{ Str::limit($product->description, 60) }}</div>
                                        @if($product->is_featured)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 mt-1">
                                                ⭐ Vedette
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($product->promotional_price)
                                    <div class="text-sm font-bold text-green-600">{{ number_format($product->promotional_price, 0, ',', ' ') }} F</div>
                                    <div class="text-xs text-gray-400 line-through">{{ number_format($product->price, 0, ',', ' ') }} F</div>
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-red-100 text-red-600 text-xs font-bold rounded">PROMO</span>
                                @else
                                    <div class="text-sm font-bold text-gray-900">{{ number_format($product->price, 0, ',', ' ') }} F</div>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($product->stock_quantity > 10)
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-sm font-bold text-green-600">{{ $product->stock_quantity }}</span>
                                    @elseif($product->stock_quantity > 0)
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-2 animate-pulse"></span>
                                        <span class="text-sm font-bold text-yellow-600">{{ $product->stock_quantity }}</span>
                                    @else
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                        <span class="text-sm font-bold text-red-600">Rupture</span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($product->hasImages())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            📸 {{ $product->images_count }}
                                        </span>
                                    @endif
                                    @if($product->hasVideos())
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                            🎥 {{ $product->videos_count }}
                                        </span>
                                    @endif
                                    @if(!$product->hasImages() && !$product->hasVideos())
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                @if($product->is_active)
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
                                    <a href="{{ route('admin.ad-campaigns.create', ['products' => [$product->id]]) }}"
                                       class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110"
                                       title="Créer campagne publicitaire">
                                        <span class="text-xl">📢</span>
                                    </a>

                                    <a href="{{ route('products.show', $product->slug) }}"
                                       target="_blank"
                                       class="text-green-600 hover:text-green-800 transition transform hover:scale-110"
                                       title="Voir sur le site">
                                        <span class="text-xl">👁️</span>
                                    </a>

                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-indigo-600 hover:text-indigo-800 transition transform hover:scale-110"
                                       title="Modifier">
                                        <span class="text-xl">✏️</span>
                                    </a>

                                    <button type="button"
                                            onclick="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                            class="text-red-600 hover:text-red-800 transition transform hover:scale-110"
                                            title="Supprimer">
                                        <span class="text-xl">🗑️</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <span class="text-7xl opacity-20 mb-4">📦</span>
                                    <p class="text-xl text-gray-500 font-semibold mb-2">Aucun produit trouvé</p>
                                    <p class="text-sm text-gray-400 mb-4">Essayez de modifier vos filtres</p>
                                    @if(request()->hasAny(['search', 'category', 'status']))
                                        <a href="{{ route('admin.products.index') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                            Réinitialiser les filtres
                                        </a>
                                    @else
                                        <a href="{{ route('admin.products.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                            ➕ Créer votre premier produit
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
                        Êtes-vous sûr de vouloir supprimer le produit :
                    </p>
                    <p class="text-base font-semibold text-gray-900 mb-4">
                        "<span id="productName"></span>" ?
                    </p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                        <p class="text-xs text-yellow-800">
                            ⚠️ Cette action est irréversible et supprimera également toutes les images et vidéos associées.
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

    {{-- Modal campagne publicitaire --}}
    <div id="advertiseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6 pb-4 border-b">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">📢 Créer une campagne publicitaire</h3>
                        <p class="text-sm text-gray-600 mt-1">Boostez la visibilité de vos produits</p>
                    </div>
                    <button onclick="closeAdvertiseModal()" 
                            class="text-gray-400 hover:text-gray-600 transition">
                        <span class="text-3xl font-light">&times;</span>
                    </button>
                </div>

                <form id="campaignForm" method="GET" action="{{ route('admin.ad-campaigns.create') }}">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Produits sélectionnés</label>
                            <div id="selectedProductsList" class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-lg border border-gray-200 min-h-[100px] max-h-[200px] overflow-y-auto">
                                <p class="text-gray-500 text-sm text-center py-8">Aucun produit sélectionné</p>
                            </div>
                            <input type="hidden" name="products" id="selectedProductsInput">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">💰 Budget (FCFA)</label>
                                <select name="budget" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="10000">10 000 FCFA</option>
                                    <option value="25000" selected>25 000 FCFA</option>
                                    <option value="50000">50 000 FCFA</option>
                                    <option value="100000">100 000 FCFA</option>
                                    <option value="250000">250 000 FCFA</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Durée</label>
                                <select name="duration" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="7">7 jours</option>
                                    <option value="14" selected>14 jours</option>
                                    <option value="30">30 jours</option>
                                    <option value="60">60 jours</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">🎯 Plateformes publicitaires</label>
                            <div class="space-y-3">
                                <label class="flex items-start p-4 border-2 border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 cursor-pointer transition group">
                                    <input type="radio" name="platform" value="both" checked class="mt-1 mr-4 text-blue-600 focus:ring-blue-500 w-5 h-5">
                                    <div>
                                        <div class="font-bold text-gray-900 group-hover:text-blue-600">Google Ads + Meta Ads</div>
                                        <div class="text-sm text-gray-600 mt-1">Portée maximale - Recommandé 🚀</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-4 border-2 border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 cursor-pointer transition group">
                                    <input type="radio" name="platform" value="google_ads" class="mt-1 mr-4 text-blue-600 focus:ring-blue-500 w-5 h-5">
                                    <div>
                                        <div class="font-bold text-gray-900 group-hover:text-blue-600">Google Ads uniquement</div>
                                        <div class="text-sm text-gray-600 mt-1">Recherche Google et sites partenaires</div>
                                    </div>
                                </label>
                                <label class="flex items-start p-4 border-2 border-gray-300 rounded-lg hover:bg-blue-50 hover:border-blue-500 cursor-pointer transition group">
                                    <input type="radio" name="platform" value="meta_ads" class="mt-1 mr-4 text-blue-600 focus:ring-blue-500 w-5 h-5">
                                    <div>
                                        <div class="font-bold text-gray-900 group-hover:text-blue-600">Meta Ads uniquement</div>
                                        <div class="text-sm text-gray-600 mt-1">Facebook et Instagram</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-8 pt-6 border-t">
                        <button type="button" onclick="closeAdvertiseModal()"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-4 rounded-lg transition">
                            Annuler
                        </button>
                        <button type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition">
                            Créer la campagne →
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

// Mise à jour de la sélection
function updateSelection() {
    const checkboxes = document.querySelectorAll('.product-checkbox:checked');
    selectedProducts = Array.from(checkboxes).map(cb => ({
        id: cb.value,
        name: cb.getAttribute('data-name')
    }));

    const count = selectedProducts.length;
    document.getElementById('selectedCount').textContent = `${count} sélectionné${count > 1 ? 's' : ''}`;

    // Activer/désactiver les boutons
    const hasSelection = count > 0;
    document.getElementById('bulkAdvertiseBtn').disabled = !hasSelection;
    document.getElementById('selectedAdvertiseBtn').disabled = !hasSelection;

    // Gestion "Sélectionner tout"
    const totalCheckboxes = document.querySelectorAll('.product-checkbox').length;
    const selectAllCheckbox = document.getElementById('selectAll');
    const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');

    if (count === 0) {
        selectAllCheckbox.indeterminate = false;
        selectAllCheckbox.checked = false;
        selectAllHeaderCheckbox.indeterminate = false;
        selectAllHeaderCheckbox.checked = false;
    } else if (count === totalCheckboxes) {
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
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
    document.getElementById('selectAllHeader').checked = this.checked;
    updateSelection();
});

document.getElementById('selectAllHeader')?.addEventListener('change', function() {
    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
    document.getElementById('selectAll').checked = this.checked;
    updateSelection();
});

// Modal publicité
function openAdvertiseModal() {
    if (selectedProducts.length === 0) {
        if (window.showAdminNotification) {
            window.showAdminNotification('Veuillez sélectionner au moins un produit', 'warning');
        } else {
            alert('⚠️ Veuillez sélectionner au moins un produit');
        }
        return;
    }

    const productsList = document.getElementById('selectedProductsList');
    productsList.innerHTML = selectedProducts.map(product =>
        `<div class="flex items-center justify-between bg-white p-3 rounded-lg mb-2 border border-gray-200 hover:border-blue-300 transition">
            <span class="text-sm font-semibold text-gray-900">${product.name}</span>
            <button type="button" onclick="removeProduct('${product.id}')" 
                    class="text-red-500 hover:text-red-700 font-bold text-xl transition"
                    title="Retirer ce produit">×</button>
        </div>`
    ).join('');

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

// Modal suppression
function confirmDelete(productId, productName) {
    document.getElementById('productName').textContent = productName;
    document.getElementById('deleteForm').action = `/admin/products/${productId}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Fermer modals en cliquant à l'extérieur
[document.getElementById('deleteModal'), document.getElementById('advertiseModal')].forEach(modal => {
    modal?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });
});

// Initialisation
document.addEventListener('DOMContentLoaded', updateSelection);
console.log('✅ Page gestion produits initialisée');
</script>
@endpush

@endsection

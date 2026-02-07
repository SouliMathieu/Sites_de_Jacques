@extends('admin.layouts.app')

@section('title', 'Tableau de bord - Jackson Energy International')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-montserrat font-bold text-gray-900">📊 Tableau de bord</h1>
    <p class="text-gray-600 mt-1">Vue d'ensemble de votre boutique Jackson Energy</p>
</div>

<!-- Statistiques principales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Produits -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg">
                <span class="text-3xl">📦</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Produits</p>
                <p class="text-3xl font-bold text-gray-900">{{ $stats['total_products'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Produits Actifs -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-lg">
                <span class="text-3xl">✅</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Produits Actifs</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_products'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Catégories -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg">
                <span class="text-3xl">📁</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Catégories</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['total_categories'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Stock Faible -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-orange-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg">
                <span class="text-3xl">⚠️</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Stock Faible</p>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['low_stock_products'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques des commandes -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Commandes -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-indigo-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg">
                <span class="text-3xl">📋</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Commandes</p>
                <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_orders'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- En attente -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-yellow-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-lg">
                <span class="text-3xl">⏳</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En attente</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_orders'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Confirmées -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 rounded-lg">
                <span class="text-3xl">✅</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Confirmées</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['confirmed_orders'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Chiffre d'affaires -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 border-l-4 border-emerald-500">
        <div class="flex items-center">
            <div class="p-3 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-lg">
                <span class="text-3xl">💰</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Chiffre d'affaires</p>
                <p class="text-2xl font-bold text-emerald-600">{{ number_format($stats['total_revenue'] ?? 0, 0, ',', ' ') }} F</p>
            </div>
        </div>
    </div>
</div>

<!-- Sections principales -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Produits récents -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">🆕 Produits récents</h2>
                <a href="{{ route('admin.products.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Voir tout →
                </a>
            </div>
        </div>
        <div class="p-6">
            @forelse($recent_products ?? [] as $product)
            <div class="flex items-center justify-between py-4 border-b last:border-b-0 hover:bg-gray-50 transition-colors rounded px-2">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="inline-flex items-center">
                            📁 {{ $product->category->name ?? 'Sans catégorie' }}
                        </span>
                    </p>
                </div>
                <div class="text-right ml-4">
                    <p class="font-bold text-green-600">{{ number_format($product->price ?? 0, 0, ',', ' ') }} F</p>
                    <p class="text-sm text-gray-600 mt-1">
                        Stock: <span class="font-medium {{ ($product->stock_quantity ?? 0) < 10 ? 'text-orange-600' : 'text-gray-900' }}">
                            {{ $product->stock_quantity ?? 0 }}
                        </span>
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <span class="text-6xl opacity-20">📦</span>
                <p class="text-gray-500 mt-4">Aucun produit récent</p>
                <a href="{{ route('admin.products.create') }}" class="inline-block mt-3 text-blue-600 hover:text-blue-800 font-medium">
                    + Ajouter un produit
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Alertes stock -->
    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-white">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">⚠️ Alertes stock faible</h2>
                <span class="text-sm text-orange-600 font-medium">
                    {{ count($low_stock_products ?? []) }} produit(s)
                </span>
            </div>
        </div>
        <div class="p-6">
            @forelse($low_stock_products ?? [] as $product)
            <div class="flex items-center justify-between py-4 border-b last:border-b-0 hover:bg-orange-50 transition-colors rounded px-2">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $product->category->name ?? 'Sans catégorie' }}</p>
                </div>
                <div class="text-right ml-4">
                    <span class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-full text-sm font-semibold">
                        {{ $product->stock_quantity ?? 0 }} restant(s)
                    </span>
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="block mt-2 text-xs text-blue-600 hover:text-blue-800">
                        Réapprovisionner →
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <span class="text-6xl opacity-20">✅</span>
                <p class="text-gray-500 mt-4">Aucune alerte de stock</p>
                <p class="text-sm text-gray-400 mt-2">Tous les produits ont un bon stock</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Commandes récentes -->
<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow mb-8">
    <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-white">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">📋 Commandes récentes</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-green-600 hover:text-green-800 font-medium">
                Voir toutes →
            </a>
        </div>
    </div>
    <div class="p-6">
        @forelse($recent_orders ?? [] as $order)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between py-4 border-b last:border-b-0 hover:bg-gray-50 transition-colors rounded px-2 gap-4">
            <div class="flex-1">
                <p class="font-bold text-gray-900">#{{ $order->order_number }}</p>
                <p class="text-sm text-gray-600 mt-1">
                    👤 {{ $order->customer->name ?? 'Client' }} • 
                    <span class="font-medium">{{ $order->orderItems->count() }}</span> produit(s)
                </p>
            </div>
            <div class="text-left sm:text-right">
                <p class="font-bold text-green-600 text-lg">{{ number_format($order->total_amount ?? 0, 0, ',', ' ') }} F</p>
                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full mt-2
                    @switch($order->status ?? 'pending')
                        @case('pending') bg-yellow-100 text-yellow-800 @break
                        @case('confirmed') bg-green-100 text-green-800 @break
                        @case('processing') bg-blue-100 text-blue-800 @break
                        @case('shipped') bg-indigo-100 text-indigo-800 @break
                        @case('delivered') bg-emerald-100 text-emerald-800 @break
                        @case('cancelled') bg-red-100 text-red-800 @break
                        @default bg-gray-100 text-gray-800
                    @endswitch">
                    @switch($order->status ?? 'pending')
                        @case('pending') ⏳ En attente @break
                        @case('confirmed') ✅ Confirmée @break
                        @case('processing') 🔄 En préparation @break
                        @case('shipped') 🚚 Expédiée @break
                        @case('delivered') 📦 Livrée @break
                        @case('cancelled') ❌ Annulée @break
                        @default 🔍 Statut inconnu
                    @endswitch
                </span>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <span class="text-6xl opacity-20">📋</span>
            <p class="text-gray-500 mt-4">Aucune commande récente</p>
            <p class="text-sm text-gray-400 mt-2">Les nouvelles commandes apparaîtront ici</p>
        </div>
        @endforelse
    </div>
</div>

<!-- Actions rapides -->
<div class="mt-8">
    <h2 class="text-xl font-bold mb-6 text-gray-900">⚡ Actions rapides</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('admin.products.create') }}" class="group bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-xl transition-all transform hover:scale-105">
            <div class="text-4xl mb-3">➕</div>
            <div class="font-bold text-lg">Ajouter un produit</div>
            <p class="text-sm text-green-100 mt-2">Créer un nouveau produit</p>
        </a>
        
        <a href="{{ route('admin.categories.create') }}" class="group bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-lg hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-xl transition-all transform hover:scale-105">
            <div class="text-4xl mb-3">📁</div>
            <div class="font-bold text-lg">Ajouter une catégorie</div>
            <p class="text-sm text-blue-100 mt-2">Organiser vos produits</p>
        </a>
        
        <a href="{{ route('home') }}" target="_blank" class="group bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-lg hover:from-orange-600 hover:to-orange-700 shadow-md hover:shadow-xl transition-all transform hover:scale-105">
            <div class="text-4xl mb-3">🌐</div>
            <div class="font-bold text-lg">Voir le site</div>
            <p class="text-sm text-orange-100 mt-2">Aperçu public du site</p>
        </a>
    </div>
</div>

@endsection

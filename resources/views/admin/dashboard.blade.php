@extends('admin.layouts.app')

@section('title', 'Tableau de bord - Administration')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-montserrat font-bold text-gray-900">Tableau de bord</h1>
    <p class="text-gray-600">Vue d'ensemble de votre boutique</p>
</div>

<!-- Statistiques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
                <span class="text-2xl">📦</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Produits</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_products'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
                <span class="text-2xl">✅</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Produits Actifs</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_products'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
                <span class="text-2xl">📁</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Catégories</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_categories'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-2 bg-orange-100 rounded-lg">
                <span class="text-2xl">⚠️</span>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Stock Faible</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['low_stock_products'] }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Produits récents -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Produits récents</h2>
        </div>
        <div class="p-6">
            @forelse($recent_products as $product)
            <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                <div>
                    <p class="font-medium">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>
                    <p class="text-sm text-gray-600">Stock: {{ $product->stock_quantity }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500">Aucun produit récent</p>
            @endforelse
        </div>
    </div>

    <!-- Alertes stock -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold">Alertes stock faible</h2>
        </div>
        <div class="p-6">
            @forelse($low_stock_products as $product)
            <div class="flex items-center justify-between py-3 border-b last:border-b-0">
                <div>
                    <p class="font-medium">{{ $product->name }}</p>
                    <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-sm">
                        {{ $product->stock_quantity }} restant(s)
                    </span>
                </div>
            </div>
            @empty
            <p class="text-gray-500">Aucune alerte de stock</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="mt-8">
    <h2 class="text-xl font-semibold mb-4">Actions rapides</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.products.create') }}" class="bg-vert-energie text-white p-4 rounded-lg hover:bg-green-700 transition text-center">
            <div class="text-2xl mb-2">➕</div>
            <div class="font-medium">Ajouter un produit</div>
        </a>
        
        <a href="{{ route('admin.categories.create') }}" class="bg-bleu-tech text-white p-4 rounded-lg hover:bg-blue-700 transition text-center">
            <div class="text-2xl mb-2">📁</div>
            <div class="font-medium">Ajouter une catégorie</div>
        </a>
        
        <a href="{{ route('home') }}" target="_blank" class="bg-orange-burkina text-white p-4 rounded-lg hover:bg-orange-600 transition text-center">
            <div class="text-2xl mb-2">🌐</div>
            <div class="font-medium">Voir le site</div>
        </a>
    </div>
</div>
<!-- Ajoutez ces cartes après les statistiques existantes -->
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        <div class="p-2 bg-indigo-100 rounded-lg">
            <span class="text-2xl">📋</span>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total Commandes</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        <div class="p-2 bg-yellow-100 rounded-lg">
            <span class="text-2xl">⏳</span>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">En attente</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_orders'] }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        <div class="p-2 bg-green-100 rounded-lg">
            <span class="text-2xl">✅</span>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Confirmées</p>
            <p class="text-2xl font-semibold text-gray-900">{{ $stats['confirmed_orders'] }}</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center">
        <div class="p-2 bg-emerald-100 rounded-lg">
            <span class="text-2xl">💰</span>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Chiffre d'affaires</p>
            <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
</div>
<!-- Ajoutez après la section des produits récents -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <h2 class="text-lg font-semibold">Commandes récentes</h2>
    </div>
    <div class="p-6">
        @forelse($recent_orders as $order)
        <div class="flex items-center justify-between py-3 border-b last:border-b-0">
            <div>
                <p class="font-medium">{{ $order->order_number }}</p>
                <p class="text-sm text-gray-600">{{ $order->customer->name }} - {{ $order->orderItems->count() }} produit(s)</p>
            </div>
            <div class="text-right">
                <p class="font-semibold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                <span class="px-2 py-1 text-xs rounded-full {{ $order->status_badge }}">
                    @switch($order->status)
                        @case('pending') ⏳ En attente @break
                        @case('confirmed') ✅ Confirmée @break
                        @case('processing') 🔄 En préparation @break
                        @case('shipped') 🚚 Expédiée @break
                        @case('delivered') 📦 Livrée @break
                        @case('cancelled') ❌ Annulée @break
                    @endswitch
                </span>
            </div>
        </div>
        @empty
        <p class="text-gray-500">Aucune commande récente</p>
        @endforelse
    </div>
</div>

@endsection

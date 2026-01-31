@extends('admin.layouts.app')

@section('title', 'Détails de la campagne - Administration')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-montserrat font-bold text-gray-900">{{ $adCampaign->name }}</h1>
            <p class="text-gray-600">Détails de la campagne publicitaire</p>
        </div>
        <div class="flex space-x-3">
            @if($adCampaign->status === 'draft')
                <form method="POST" action="{{ route('admin.ad-campaigns.launch', $adCampaign) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                        🚀 Générer les liens
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.ad-campaigns.index') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                ← Retour
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Informations de la campagne --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Informations générales</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Statut</label>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($adCampaign->status === 'active') bg-green-100 text-green-800
                    @elseif($adCampaign->status === 'draft') bg-yellow-100 text-yellow-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($adCampaign->status) }}
                </span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600">Plateforme</label>
                <div class="mt-1">
                    @if($adCampaign->platform === 'both')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800 mr-2">Google Ads</span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">Meta Ads</span>
                    @elseif($adCampaign->platform === 'google_ads')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">Google Ads</span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">Meta Ads</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Budget</label>
                    <div class="text-lg font-semibold">{{ number_format($adCampaign->budget, 0, ',', ' ') }} FCFA</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Durée</label>
                    <div class="text-lg font-semibold">{{ $adCampaign->duration_days }} jours</div>
                </div>
            </div>

            @if($adCampaign->ad_copy)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Texte publicitaire</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg text-sm">{{ $adCampaign->ad_copy }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Liens vers les plateformes --}}
    @if($adCampaign->performance_data && (isset($adCampaign->performance_data['google_ads_link']) || isset($adCampaign->performance_data['meta_ads_link'])))
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">🔗 Liens des plateformes publicitaires</h2>
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                    <h3 class="font-medium text-blue-900 mb-2">Instructions importantes :</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Cliquez sur les liens ci-dessous pour accéder aux plateformes</li>
                        <li>• Les paramètres de base sont pré-remplis</li>
                        <li>• Connectez-vous avec vos comptes publicitaires</li>
                        <li>• Ajustez les paramètres selon vos besoins</li>
                        <li>• Lancez les campagnes manuellement</li>
                    </ul>
                </div>

                @if(isset($adCampaign->performance_data['google_ads_link']))
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-blue-600 font-bold">G</span>
                                </div>
                                <div>
                                    <h4 class="font-medium">Google Ads</h4>
                                    <p class="text-sm text-gray-500">Créer une campagne de recherche</p>
                                </div>
                            </div>
                            <a href="{{ $adCampaign->performance_data['google_ads_link'] }}"
                               target="_blank"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                Ouvrir Google Ads →
                            </a>
                        </div>
                    </div>
                @endif

                @if(isset($adCampaign->performance_data['meta_ads_link']))
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-purple-600 font-bold">M</span>
                                </div>
                                <div>
                                    <h4 class="font-medium">Meta Ads</h4>
                                    <p class="text-sm text-gray-500">Facebook & Instagram</p>
                                </div>
                            </div>
                            <a href="{{ $adCampaign->performance_data['meta_ads_link'] }}"
                               target="_blank"
                               class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                Ouvrir Meta Ads →
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Lien direct vers le compte publicitaire Meta si disponible --}}
                @if($adCampaign->platform === 'meta_ads' || $adCampaign->platform === 'both')
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-gray-600 font-bold">📊</span>
                                </div>
                                <div>
                                    <h4 class="font-medium">Accès direct au compte publicitaire</h4>
                                    <p class="text-sm text-gray-500">Business Manager - Gestionnaire de publicités</p>
                                </div>
                            </div>
                            <a href="https://business.facebook.com/adsmanager/"
                               target="_blank"
                               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                                Ouvrir Business Manager →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Produits de la campagne --}}
<div class="mt-8">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Produits de la campagne ({{ $products->count() }})</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-center space-x-4">
                            <img src="{{ $product->first_image }}" alt="{{ $product->name }}"
                                 class="w-16 h-16 object-cover rounded-lg">
                            <div class="flex-1">
                                <h3 class="font-medium">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</p>
                                <a href="{{ route('products.show', $product->slug) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    Voir le produit →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- Guide d'utilisation --}}
<div class="mt-8">
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-yellow-800 mb-4">📋 Guide d'utilisation</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-yellow-700">
            <div>
                <h4 class="font-medium mb-2">Pour Google Ads :</h4>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Cliquez sur le lien "Ouvrir Google Ads"</li>
                    <li>Connectez-vous à votre compte Google Ads</li>
                    <li>Vérifiez les mots-clés pré-remplis</li>
                    <li>Ajustez le budget quotidien selon vos besoins</li>
                    <li>Configurez les annonces et lancez la campagne</li>
                </ol>
            </div>
            <div>
                <h4 class="font-medium mb-2">Pour Meta Ads :</h4>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Cliquez sur le lien "Ouvrir Meta Ads"</li>
                    <li>Connectez-vous à votre Business Manager</li>
                    <li>Sélectionnez votre compte publicitaire</li>
                    <li>Configurez l'audience et le budget</li>
                    <li>Créez vos visuels et lancez la campagne</li>
                </ol>
            </div>
        </div>
    </div>
</div>

@endsection

@extends('admin.layouts.app')

@section('title', 'Campagnes publicitaires - Administration')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-montserrat font-bold text-gray-900">Campagnes publicitaires</h1>
            <p class="text-gray-600">Gérez vos campagnes Google Ads et Meta Ads</p>
        </div>
        <a href="{{ route('admin.ad-campaigns.create') }}"
           class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            ➕ Nouvelle campagne
        </a>
    </div>
</div>

{{-- Statistiques rapides --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">📢</div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total campagnes</p>
                <p class="text-2xl font-bold text-gray-900">{{ $campaigns->total() ?? 0 }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">✅</div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Campagnes actives</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\AdCampaign::where('status', 'active')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">📝</div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Brouillons</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\AdCampaign::where('status', 'draft')->count() }}</p>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">💰</div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Budget total</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format(\App\Models\AdCampaign::sum('budget'), 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
    </div>
</div>

{{-- Table des campagnes --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campagne</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plateforme</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produits</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($campaigns as $campaign)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                            <div class="text-sm text-gray-500">Créée le {{ $campaign->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($campaign->platform === 'both')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-1">Google</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Meta</span>
                            @elseif($campaign->platform === 'google_ads')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Google Ads</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Meta Ads</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ count($campaign->product_ids ?? []) }} produit(s)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($campaign->budget, 0, ',', ' ') }} FCFA</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($campaign->status === 'active') bg-green-100 text-green-800
                                @elseif($campaign->status === 'draft') bg-yellow-100 text-yellow-800
                                @elseif($campaign->status === 'paused') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($campaign->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $campaign->duration_days }} jours
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.ad-campaigns.show', $campaign) }}"
                                   class="text-blue-600 hover:text-blue-900 transition"
                                   title="Voir les détails">👁️</a>

                                @if($campaign->status === 'draft')
                                    <form method="POST" action="{{ route('admin.ad-campaigns.launch', $campaign) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="text-green-600 hover:text-green-900 transition"
                                                title="Lancer la campagne">🚀</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <p class="text-xl mb-2">📢</p>
                                <p>Aucune campagne publicitaire trouvée</p>
                                <a href="{{ route('admin.ad-campaigns.create') }}"
                                   class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                    Créer votre première campagne
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($campaigns && $campaigns->hasPages())
    <div class="mt-6">
        {{ $campaigns->links() }}
    </div>
@endif

@endsection

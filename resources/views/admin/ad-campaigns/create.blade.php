@extends('admin.layouts.app')

@section('title', 'Créer une campagne publicitaire - Administration')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-montserrat font-bold text-gray-900">Créer une campagne publicitaire</h1>
    <p class="text-gray-600">Lancez une campagne publicitaire pour vos produits sur Google Ads et Meta Ads</p>
</div>

<div class="bg-white rounded-lg shadow">
    <form method="POST" action="{{ route('admin.ad-campaigns.store') }}" class="p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Informations de base --}}
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom de la campagne *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="Ex: Promo Panneaux Solaires Décembre 2024"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Plateforme publicitaire *</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <input type="radio" name="platform" value="both" {{ old('platform', 'both') == 'both' ? 'checked' : '' }} class="mr-3">
                            <div>
                                <div class="font-medium">Google Ads + Meta Ads</div>
                                <div class="text-sm text-gray-500">Portée maximale - Recommandé</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <input type="radio" name="platform" value="google_ads" {{ old('platform') == 'google_ads' ? 'checked' : '' }} class="mr-3">
                            <div>
                                <div class="font-medium">Google Ads uniquement</div>
                                <div class="text-sm text-gray-500">Recherche Google et sites partenaires</div>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <input type="radio" name="platform" value="meta_ads" {{ old('platform') == 'meta_ads' ? 'checked' : '' }} class="mr-3">
                            <div>
                                <div class="font-medium">Meta Ads uniquement</div>
                                <div class="text-sm text-gray-500">Facebook et Instagram</div>
                            </div>
                        </label>
                    </div>
                    @error('platform')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">Budget total (FCFA) *</label>
                        <select id="budget" name="budget" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('budget') border-red-500 @enderror">
                            <option value="">Choisir un budget</option>
                            <option value="10000" {{ old('budget') == '10000' ? 'selected' : '' }}>10 000 FCFA</option>
                            <option value="25000" {{ old('budget') == '25000' ? 'selected' : '' }}>25 000 FCFA</option>
                            <option value="50000" {{ old('budget') == '50000' ? 'selected' : '' }}>50 000 FCFA</option>
                            <option value="100000" {{ old('budget') == '100000' ? 'selected' : '' }}>100 000 FCFA</option>
                            <option value="250000" {{ old('budget') == '250000' ? 'selected' : '' }}>250 000 FCFA</option>
                            <option value="500000" {{ old('budget') == '500000' ? 'selected' : '' }}>500 000 FCFA</option>
                        </select>
                        @error('budget')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="duration_days" class="block text-sm font-medium text-gray-700 mb-2">Durée (jours) *</label>
                        <select id="duration_days" name="duration_days" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('duration_days') border-red-500 @enderror">
                            <option value="">Choisir une durée</option>
                            <option value="7" {{ old('duration_days') == '7' ? 'selected' : '' }}>7 jours</option>
                            <option value="14" {{ old('duration_days') == '14' ? 'selected' : '' }}>14 jours</option>
                            <option value="30" {{ old('duration_days') == '30' ? 'selected' : '' }}>30 jours</option>
                            <option value="60" {{ old('duration_days') == '60' ? 'selected' : '' }}>60 jours</option>
                            <option value="90" {{ old('duration_days') == '90' ? 'selected' : '' }}>90 jours</option>
                        </select>
                        @error('duration_days')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="ad_copy" class="block text-sm font-medium text-gray-700 mb-2">Texte publicitaire</label>
                    <textarea id="ad_copy" name="ad_copy" rows="4"
                              placeholder="Rédigez un message accrocheur pour votre publicité..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('ad_copy') }}</textarea>
                    <small class="text-gray-500">Laissez vide pour une génération automatique basée sur vos produits</small>
                </div>
            </div>

            {{-- Sélection des produits --}}
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Produits à promouvoir *</label>

                    @if(!empty($selectedProducts) && $selectedProducts->count() > 0)
                        <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-medium text-blue-900 mb-2">Produits pré-sélectionnés :</h4>
                            @foreach($selectedProducts as $product)
                                <div class="flex items-center mb-2">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" checked
                                           class="mr-2" id="preselected_{{ $product->id }}">
                                    <label for="preselected_{{ $product->id }}" class="flex items-center">
                                        <img src="{{ $product->first_image }}" alt="{{ $product->name }}"
                                             class="w-12 h-12 object-cover rounded mr-3">
                                        <div>
                                            <div class="font-medium">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="max-h-96 overflow-y-auto border border-gray-300 rounded-lg">
                        @foreach($products as $product)
                            @if(empty($selectedProducts) || !$selectedProducts->contains('id', $product->id))
                                <label class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-200 last:border-b-0">
                                    <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="mr-3"
                                           {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                                    <img src="{{ $product->first_image }}" alt="{{ $product->name }}"
                                         class="w-16 h-16 object-cover rounded mr-4">
                                    <div class="flex-1">
                                        <div class="font-medium">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($product->description, 80) }}</div>
                                        <div class="text-sm font-medium text-green-600">{{ number_format($product->current_price, 0, ',', ' ') }} FCFA</div>
                                        @if($product->is_featured)
                                            <span class="inline-block px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">⭐ Vedette</span>
                                        @endif
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('product_ids')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Ciblage --}}
        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Ciblage de l'audience (Optionnel)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Localisation</label>
                    <select name="target_audience[location]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Toutes les localisations</option>
                        <option value="ouagadougou">Ouagadougou</option>
                        <option value="bobo-dioulasso">Bobo-Dioulasso</option>
                        <option value="burkina-faso">Tout le Burkina Faso</option>
                        <option value="west-africa">Afrique de l'Ouest</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Âge</label>
                    <select name="target_audience[age]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Tous les âges</option>
                        <option value="18-24">18-24 ans</option>
                        <option value="25-34">25-34 ans</option>
                        <option value="35-44">35-44 ans</option>
                        <option value="45-54">45-54 ans</option>
                        <option value="55+">55+ ans</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Intérêts</label>
                    <select name="target_audience[interests]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Tous les intérêts</option>
                        <option value="solar-energy">Énergie solaire</option>
                        <option value="renewable-energy">Énergie renouvelable</option>
                        <option value="home-improvement">Amélioration de l'habitat</option>
                        <option value="technology">Technologie</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Aperçu du budget --}}
        <div class="bg-gray-50 p-6 rounded-lg">
            <h4 class="font-medium text-gray-900 mb-4">Estimation de la campagne</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Budget journalier estimé :</span>
                    <div class="font-medium" id="dailyBudget">Sélectionnez un budget</div>
                </div>
                <div>
                    <span class="text-gray-600">Portée estimée :</span>
                    <div class="font-medium" id="estimatedReach">Sélectionnez un budget</div>
                </div>
                <div>
                    <span class="text-gray-600">Clics estimés :</span>
                    <div class="font-medium" id="estimatedClicks">Sélectionnez un budget</div>
                </div>
            </div>
        </div>

        <div class="flex justify-between pt-6 border-t">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                ← Retour aux produits
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                🚀 Créer la campagne
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Calcul dynamique des estimations
function updateEstimates() {
    const budget = document.getElementById('budget').value;
    const duration = document.getElementById('duration_days').value;

    if (budget && duration) {
        const dailyBudget = Math.round(budget / duration);
        const estimatedReach = Math.round(budget * 2.5); // Estimation basique
        const estimatedClicks = Math.round(budget / 50); // 50 FCFA par clic estimé

        document.getElementById('dailyBudget').textContent = `${dailyBudget.toLocaleString()} FCFA/jour`;
        document.getElementById('estimatedReach').textContent = `${estimatedReach.toLocaleString()} personnes`;
        document.getElementById('estimatedClicks').textContent = `${estimatedClicks.toLocaleString()} clics`;
    }
}

document.getElementById('budget').addEventListener('change', updateEstimates);
document.getElementById('duration_days').addEventListener('change', updateEstimates);

// Appel initial
updateEstimates();
</script>
@endpush

@endsection

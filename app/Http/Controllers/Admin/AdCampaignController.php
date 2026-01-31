<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdCampaign;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdCampaignController extends Controller
{
    public function index()
    {
        $campaigns = AdCampaign::with('creator')
            ->latest()
            ->paginate(15);

        return view('admin.ad-campaigns.index', compact('campaigns'));
    }

    public function create(Request $request)
    {
        $products = Product::where('is_active', true)->get();
        $selectedProducts = [];

        // Si des produits sont pré-sélectionnés via URL
        if ($request->has('products')) {
            $productIds = is_array($request->products) ? $request->products : explode(',', $request->products);
            $selectedProducts = Product::whereIn('id', $productIds)->get();
        }

        return view('admin.ad-campaigns.create', compact('products', 'selectedProducts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'platform' => 'required|in:google_ads,meta_ads,both',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'budget' => 'required|numeric|min:1000', // Minimum 1000 FCFA
            'duration_days' => 'required|integer|min:1|max:365',
            'target_audience' => 'nullable|array',
            'ad_copy' => 'nullable|string|max:500',
        ]);

        $campaign = AdCampaign::create([
            'name' => $request->name,
            'platform' => $request->platform,
            'product_ids' => $request->product_ids,
            'budget' => $request->budget,
            'duration_days' => $request->duration_days,
            'target_audience' => $request->target_audience ?? [],
            'ad_copy' => $request->ad_copy,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.ad-campaigns.show', $campaign)
            ->with('success', 'Campagne publicitaire créée avec succès !');
    }

    public function show(AdCampaign $adCampaign)
    {
        $adCampaign->load('creator');
        $products = $adCampaign->products();

        return view('admin.ad-campaigns.show', compact('adCampaign', 'products'));
    }

    public function launch(AdCampaign $adCampaign)
{
    try {
        // Au lieu d'appeler les APIs, générer les liens directs
        $links = $this->generatePlatformLinks($adCampaign);

        $adCampaign->update([
            'status' => 'draft', // Reste en brouillon car pas encore lancé
            'start_date' => now(),
            'end_date' => now()->addDays($adCampaign->duration_days),
            'performance_data' => [
                'google_ads_link' => $links['google_ads'] ?? null,
                'meta_ads_link' => $links['meta_ads'] ?? null,
                'generated_at' => now()->toISOString()
            ]
        ]);

        return redirect()
            ->route('admin.ad-campaigns.show', $adCampaign)
            ->with('success', 'Liens de campagne générés avec succès ! Utilisez les liens pour créer vos campagnes sur les plateformes.');

    } catch (\Exception $e) {
        Log::error('Erreur génération liens campagne', [
            'campaign_id' => $adCampaign->id,
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->back()
            ->with('error', 'Erreur lors de la génération des liens : ' . $e->getMessage());
    }
}

private function generatePlatformLinks(AdCampaign $campaign)
{
    $links = [];

    if (in_array($campaign->platform, ['google_ads', 'both'])) {
        $links['google_ads'] = $this->generateGoogleAdsLink($campaign);
    }

    if (in_array($campaign->platform, ['meta_ads', 'both'])) {
        $links['meta_ads'] = $this->generateMetaAdsLink($campaign);
    }

    return $links;
}

private function generateGoogleAdsLink(AdCampaign $campaign)
{
    // Générer un lien vers Google Ads avec paramètres pré-remplis
    $baseUrl = 'https://ads.google.com/aw/campaigns/new';

    $products = $campaign->products();
    $keywords = $products->pluck('name')->map(function($name) {
        return strtolower(str_replace(' ', '+', $name));
    })->implode(',');

    $params = [
        'campaignType' => 'SEARCH',
        'keywords' => $keywords,
        'budget' => $campaign->budget,
        'location' => 'Burkina Faso',
        'language' => 'fr'
    ];

    return $baseUrl . '?' . http_build_query($params);
}

private function generateMetaAdsLink(AdCampaign $campaign)
{
    // Lien vers Meta Business Manager
    $baseUrl = 'https://business.facebook.com/adsmanager/creation';

    $products = $campaign->products();
    $productNames = $products->pluck('name')->implode(', ');

    $params = [
        'objective' => 'CONVERSIONS',
        'campaign_name' => $campaign->name,
        'budget' => $campaign->budget,
        'duration' => $campaign->duration_days,
        'audience_location' => 'BF', // Code pays Burkina Faso
        'product_info' => urlencode($productNames)
    ];

    return $baseUrl . '?' . http_build_query($params);
}

    private function launchCampaignOnPlatforms(AdCampaign $campaign)
    {
        $results = [];

        if (in_array($campaign->platform, ['google_ads', 'both'])) {
            $results['google_id'] = $this->launchGoogleAdsCampaign($campaign);
        }

        if (in_array($campaign->platform, ['meta_ads', 'both'])) {
            $results['meta_id'] = $this->launchMetaAdsCampaign($campaign);
        }

        return $results;
    }

    private function launchGoogleAdsCampaign(AdCampaign $campaign)
    {
        // TODO: Intégration avec Google Ads API
        // Pour l'instant, simulation
        Log::info('Lancement campagne Google Ads', ['campaign' => $campaign->name]);
        return 'google_campaign_' . uniqid();
    }

    private function launchMetaAdsCampaign(AdCampaign $campaign)
    {
        // TODO: Intégration avec Meta Marketing API
        // Pour l'instant, simulation
        Log::info('Lancement campagne Meta Ads', ['campaign' => $campaign->name]);
        return 'meta_campaign_' . uniqid();
    }
}

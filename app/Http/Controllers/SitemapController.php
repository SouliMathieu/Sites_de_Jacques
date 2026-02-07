<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SitemapController extends Controller
{
    /**
     * Générer le sitemap principal (index)
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Cache du sitemap pour 24h
            $sitemap = Cache::remember('sitemap.index', 86400, function () {
                return $this->generateSitemapIndex();
            });

            return response($sitemap, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8');

        } catch (\Exception $e) {
            Log::error('Erreur génération sitemap index', [
                'error' => $e->getMessage(),
            ]);

            return response('Erreur lors de la génération du sitemap', 500);
        }
    }

    /**
     * Générer le sitemap des pages statiques
     *
     * @return \Illuminate\Http\Response
     */
    public function pages()
    {
        try {
            $sitemap = Cache::remember('sitemap.pages', 86400, function () {
                return $this->generatePagesSitemap();
            });

            return response($sitemap, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8');

        } catch (\Exception $e) {
            Log::error('Erreur génération sitemap pages', [
                'error' => $e->getMessage(),
            ]);

            return response('Erreur', 500);
        }
    }

    /**
     * Générer le sitemap des produits
     *
     * @return \Illuminate\Http\Response
     */
    public function products()
    {
        try {
            $sitemap = Cache::remember('sitemap.products', 3600, function () {
                return $this->generateProductsSitemap();
            });

            return response($sitemap, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8');

        } catch (\Exception $e) {
            Log::error('Erreur génération sitemap produits', [
                'error' => $e->getMessage(),
            ]);

            return response('Erreur', 500);
        }
    }

    /**
     * Générer le sitemap des catégories
     *
     * @return \Illuminate\Http\Response
     */
    public function categories()
    {
        try {
            $sitemap = Cache::remember('sitemap.categories', 3600, function () {
                return $this->generateCategoriesSitemap();
            });

            return response($sitemap, 200)
                ->header('Content-Type', 'application/xml; charset=UTF-8');

        } catch (\Exception $e) {
            Log::error('Erreur génération sitemap catégories', [
                'error' => $e->getMessage(),
            ]);

            return response('Erreur', 500);
        }
    }

    /**
     * Générer l'index des sitemaps
     *
     * @return string
     */
    private function generateSitemapIndex(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $sitemaps = [
            [
                'loc' => route('sitemap.pages'),
                'lastmod' => now()->toAtomString(),
            ],
            [
                'loc' => route('sitemap.categories'),
                'lastmod' => Category::latest('updated_at')->value('updated_at')?->toAtomString() ?? now()->toAtomString(),
            ],
            [
                'loc' => route('sitemap.products'),
                'lastmod' => Product::latest('updated_at')->value('updated_at')?->toAtomString() ?? now()->toAtomString(),
            ],
        ];

        foreach ($sitemaps as $sitemap) {
            $xml .= '  <sitemap>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($sitemap['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $sitemap['lastmod'] . '</lastmod>' . "\n";
            $xml .= '  </sitemap>' . "\n";
        }

        $xml .= '</sitemapindex>';

        return $xml;
    }

    /**
     * Générer le sitemap des pages statiques
     *
     * @return string
     */
    private function generatePagesSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $pages = [
            [
                'loc' => route('home'),
                'changefreq' => 'daily',
                'priority' => '1.0',
                'lastmod' => now()->toAtomString(),
            ],
            [
                'loc' => route('products.index'),
                'changefreq' => 'daily',
                'priority' => '0.9',
                'lastmod' => Product::latest('updated_at')->value('updated_at')?->toAtomString() ?? now()->toAtomString(),
            ],
            [
                'loc' => route('categories.index'),
                'changefreq' => 'weekly',
                'priority' => '0.8',
                'lastmod' => Category::latest('updated_at')->value('updated_at')?->toAtomString() ?? now()->toAtomString(),
            ],
            [
                'loc' => route('contact'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'lastmod' => now()->toAtomString(),
            ],
            [
                'loc' => route('about'),
                'changefreq' => 'monthly',
                'priority' => '0.6',
                'lastmod' => now()->toAtomString(),
            ],
        ];

        foreach ($pages as $page) {
            $xml .= $this->generateUrlEntry($page);
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Générer le sitemap des produits
     *
     * @return string
     */
    private function generateProductsSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $products = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('is_featured', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at', 'is_featured', 'images']);

        foreach ($products as $product) {
            $entry = [
                'loc' => route('products.show', $product->slug),
                'changefreq' => 'weekly',
                'priority' => $product->is_featured ? '0.9' : '0.7',
                'lastmod' => $product->updated_at->toAtomString(),
                'images' => $product->image_urls ?? [],
            ];

            $xml .= $this->generateUrlEntry($entry);
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Générer le sitemap des catégories
     *
     * @return string
     */
    private function generateCategoriesSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->get(['slug', 'updated_at', 'is_featured', 'image']);

        foreach ($categories as $category) {
            $entry = [
                'loc' => route('categories.show', $category->slug),
                'changefreq' => 'weekly',
                'priority' => $category->is_featured ? '0.9' : '0.8',
                'lastmod' => $category->updated_at->toAtomString(),
                'images' => $category->image ? [$category->image_url] : [],
            ];

            $xml .= $this->generateUrlEntry($entry);
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Générer une entrée URL pour le sitemap
     *
     * @param array $data
     * @return string
     */
    private function generateUrlEntry(array $data): string
    {
        $xml = '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($data['loc']) . '</loc>' . "\n";
        
        if (isset($data['lastmod'])) {
            $xml .= '    <lastmod>' . $data['lastmod'] . '</lastmod>' . "\n";
        }
        
        if (isset($data['changefreq'])) {
            $xml .= '    <changefreq>' . $data['changefreq'] . '</changefreq>' . "\n";
        }
        
        if (isset($data['priority'])) {
            $xml .= '    <priority>' . $data['priority'] . '</priority>' . "\n";
        }

        // Ajouter les images si disponibles
        if (!empty($data['images'])) {
            foreach (array_slice($data['images'], 0, 5) as $imageUrl) {
                $xml .= '    <image:image>' . "\n";
                $xml .= '      <image:loc>' . htmlspecialchars($imageUrl) . '</image:loc>' . "\n";
                $xml .= '    </image:image>' . "\n";
            }
        }

        $xml .= '  </url>' . "\n";

        return $xml;
    }

    /**
     * Vider le cache des sitemaps
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            Cache::forget('sitemap.index');
            Cache::forget('sitemap.pages');
            Cache::forget('sitemap.products');
            Cache::forget('sitemap.categories');

            Log::info('Cache sitemap vidé', [
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cache des sitemaps vidé avec succès !',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur vidage cache sitemap', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du vidage du cache.',
            ], 500);
        }
    }

    /**
     * Générer le fichier robots.txt dynamique
     *
     * @return \Illuminate\Http\Response
     */
    public function robots()
    {
        $robotsTxt = "User-agent: *\n";
        $robotsTxt .= "Allow: /\n\n";
        
        // Bloquer les routes admin
        $robotsTxt .= "Disallow: /admin/\n";
        $robotsTxt .= "Disallow: /login\n";
        $robotsTxt .= "Disallow: /register\n";
        $robotsTxt .= "Disallow: /profile/\n";
        $robotsTxt .= "Disallow: /cart\n";
        $robotsTxt .= "Disallow: /checkout\n\n";

        // Sitemap
        $robotsTxt .= "Sitemap: " . route('sitemap.index') . "\n";

        return response($robotsTxt, 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Ping Google pour notifier les changements du sitemap
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pingGoogle()
    {
        try {
            $sitemapUrl = urlencode(route('sitemap.index'));
            $pingUrl = "https://www.google.com/ping?sitemap={$sitemapUrl}";

            // Effectuer la requête
            $response = @file_get_contents($pingUrl);

            Log::info('Google pingé pour sitemap', [
                'sitemap_url' => route('sitemap.index'),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Google a été notifié avec succès !',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur ping Google', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la notification.',
            ], 500);
        }
    }

    /**
     * Générer et télécharger le sitemap complet
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download()
    {
        try {
            $sitemap = $this->generatePagesSitemap();

            return response()->streamDownload(function () use ($sitemap) {
                echo $sitemap;
            }, 'sitemap-' . now()->format('Y-m-d') . '.xml', [
                'Content-Type' => 'application/xml',
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur téléchargement sitemap', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors du téléchargement.');
        }
    }
}

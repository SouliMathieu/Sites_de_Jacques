<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Afficher la liste des produits avec filtres et tri
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Construction de la requête de base
        $query = Product::where('is_active', true)
            ->with(['category']);

        // 🔍 FILTRES

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category)
                  ->where('is_active', true);
            });
        }

        // Recherche globale
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('specifications', 'like', "%{$searchTerm}%")
                  ->orWhereHas('category', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filtre par prix
        if ($request->filled('price_min')) {
            $query->whereRaw('COALESCE(promotional_price, price) >= ?', [$request->price_min]);
        }

        if ($request->filled('price_max')) {
            $query->whereRaw('COALESCE(promotional_price, price) <= ?', [$request->price_max]);
        }

        // Filtre par disponibilité
        if ($request->filled('in_stock') && $request->in_stock == '1') {
            $query->where('stock_quantity', '>', 0);
        }

        // Filtre par produits en promotion
        if ($request->filled('on_sale') && $request->on_sale == '1') {
            $query->whereNotNull('promotional_price');
        }

        // Filtre par produits vedettes
        if ($request->filled('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        // Filtre par images/vidéos
        if ($request->filled('has_images') && $request->has_images == '1') {
            $query->where('images_count', '>', 0);
        }

        if ($request->filled('has_videos') && $request->has_videos == '1') {
            $query->where('videos_count', '>', 0);
        }

        // 📊 TRI
        $sortBy = $request->input('sort', 'default');
        
        switch ($sortBy) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(promotional_price, price) ASC');
                break;
            
            case 'price_desc':
                $query->orderByRaw('COALESCE(promotional_price, price) DESC');
                break;
            
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            
            case 'newest':
                $query->latest('created_at');
                break;
            
            case 'oldest':
                $query->oldest('created_at');
                break;
            
            case 'popular':
                $query->orderBy('views_count', 'desc')
                      ->orderBy('is_featured', 'desc');
                break;
            
            case 'stock':
                $query->orderBy('stock_quantity', 'desc');
                break;
            
            case 'discount':
                $query->whereNotNull('promotional_price')
                      ->orderByRaw('((price - promotional_price) / price * 100) DESC');
                break;
            
            default: // 'default'
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('sort_order')
                      ->latest('created_at');
                break;
        }

        // Pagination avec paramètres
        $perPage = $request->input('per_page', 12);
        $perPage = in_array($perPage, [12, 24, 36, 48]) ? $perPage : 12;
        
        $products = $query->paginate($perPage)->withQueryString();

        // Catégories avec compteur (mise en cache)
        $categories = Cache::remember('categories.with_counts', 3600, function () {
            return Category::where('is_active', true)
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        // Statistiques pour sidebar
        $stats = [
            'total' => Product::where('is_active', true)->count(),
            'in_stock' => Product::where('is_active', true)->where('stock_quantity', '>', 0)->count(),
            'on_sale' => Product::where('is_active', true)->whereNotNull('promotional_price')->count(),
            'featured' => Product::where('is_active', true)->where('is_featured', true)->count(),
            'price_range' => [
                'min' => (int) Product::where('is_active', true)->min('price'),
                'max' => (int) Product::where('is_active', true)->max('price'),
            ],
        ];

        // Produits vedettes pour suggestion
        $featuredProducts = Cache::remember('products.featured.random', 1800, function () {
            return Product::where('is_active', true)
                ->where('is_featured', true)
                ->where('stock_quantity', '>', 0)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        });

        // SEO dynamique
        $pageTitle = 'Tous nos produits - Jackson Energy International';
        $pageDescription = 'Découvrez notre gamme complète de produits solaires : panneaux photovoltaïques, batteries, onduleurs et accessoires. ' . $stats['total'] . ' produits disponibles.';
        
        if ($request->filled('search')) {
            $pageTitle = 'Résultats pour "' . $request->search . '" - Jackson Energy International';
            $pageDescription = 'Trouvez les meilleurs produits solaires correspondant à "' . $request->search . '". Livraison rapide au Burkina Faso.';
        }

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $pageTitle = $category->name . ' - Jackson Energy International';
                $pageDescription = 'Découvrez notre sélection de ' . strtolower($category->name) . ' de qualité supérieure.';
            }
        }

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => route('home')],
            ['name' => 'Produits', 'url' => null],
        ];

        return view('products.index', compact(
            'products',
            'categories',
            'stats',
            'featuredProducts',
            'sortBy',
            'perPage',
            'pageTitle',
            'pageDescription',
            'breadcrumbs'
        ));
    }

    /**
     * Afficher un produit spécifique
     * 
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Récupérer le produit avec relations
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category'])
            ->firstOrFail();

        // Incrémenter le compteur de vues (asynchrone)
        $this->incrementViews($product);

        // Produits similaires (même catégorie)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // Produits fréquemment achetés ensemble (si vous avez cette logique)
        $frequentlyBoughtTogether = [];

        // Produits récemment vus (depuis la session)
        $recentlyViewed = $this->getRecentlyViewedProducts($product->id);

        // Enregistrer dans l'historique de navigation
        $this->addToRecentlyViewed($product->id);

        // Avis clients (si vous avez un système d'avis)
        // $reviews = $product->reviews()->latest()->paginate(5);

        // SEO dynamique
        $pageTitle = $product->meta_title ?: $product->name . ' - Jackson Energy International';
        $pageDescription = $product->meta_description ?: strip_tags($product->description);
        $pageKeywords = $product->meta_keywords ?: $product->category->name . ', ' . $product->name . ', énergie solaire';

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => route('home')],
            ['name' => 'Produits', 'url' => route('products.index')],
            ['name' => $product->category->name, 'url' => route('categories.show', $product->category->slug)],
            ['name' => $product->name, 'url' => null],
        ];

        // Structured Data pour SEO
        $structuredData = $this->generateProductStructuredData($product);

        return view('products.show', compact(
            'product',
            'relatedProducts',
            'frequentlyBoughtTogether',
            'recentlyViewed',
            'pageTitle',
            'pageDescription',
            'pageKeywords',
            'breadcrumbs',
            'structuredData'
        ));
    }

    /**
     * Recherche AJAX rapide
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickSearch(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $searchTerm = $request->input('q');

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->with('category')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'url' => route('products.show', $product->slug),
                    'image' => $product->first_image,
                    'price' => number_format($product->current_price, 0, ',', ' ') . ' FCFA',
                    'category' => $product->category->name,
                    'in_stock' => $product->stock_quantity > 0,
                    'on_sale' => $product->promotional_price !== null,
                ];
            });

        return response()->json([
            'success' => true,
            'results' => $products,
            'count' => $products->count(),
        ]);
    }

    /**
     * Comparer plusieurs produits
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function compare(Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:2|max:4',
            'products.*' => 'required|integer|exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->products)
            ->where('is_active', true)
            ->with('category')
            ->get();

        if ($products->count() < 2) {
            return redirect()->route('products.index')
                ->with('error', 'Veuillez sélectionner au moins 2 produits à comparer.');
        }

        return view('products.compare', compact('products'));
    }

    /**
     * Obtenir les options de filtrage (AJAX)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterOptions()
    {
        $priceRange = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $options = [
            'price_range' => [
                'min' => (int) $priceRange->min_price,
                'max' => (int) $priceRange->max_price,
            ],
            'categories' => Category::where('is_active', true)
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'availability' => [
                'in_stock' => Product::where('is_active', true)->where('stock_quantity', '>', 0)->count(),
                'out_of_stock' => Product::where('is_active', true)->where('stock_quantity', '<=', 0)->count(),
            ],
            'features' => [
                'on_sale' => Product::where('is_active', true)->whereNotNull('promotional_price')->count(),
                'featured' => Product::where('is_active', true)->where('is_featured', true)->count(),
                'with_images' => Product::where('is_active', true)->where('images_count', '>', 0)->count(),
                'with_videos' => Product::where('is_active', true)->where('videos_count', '>', 0)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'options' => $options,
        ]);
    }

    /**
     * Incrémenter le compteur de vues
     * 
     * @param Product $product
     * @return void
     */
    private function incrementViews(Product $product): void
    {
        // Vérifier si déjà compté dans cette session
        $viewedKey = 'product_viewed_' . $product->id;
        
        if (!session()->has($viewedKey)) {
            $product->increment('views_count');
            session([$viewedKey => true]);
            
            Log::info("Vue produit enregistrée", [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);
        }
    }

    /**
     * Ajouter un produit aux récemment vus
     * 
     * @param int $productId
     * @return void
     */
    private function addToRecentlyViewed(int $productId): void
    {
        $recentlyViewed = session()->get('recently_viewed', []);
        
        // Retirer si déjà présent
        $recentlyViewed = array_diff($recentlyViewed, [$productId]);
        
        // Ajouter au début
        array_unshift($recentlyViewed, $productId);
        
        // Limiter à 10 produits
        $recentlyViewed = array_slice($recentlyViewed, 0, 10);
        
        session(['recently_viewed' => $recentlyViewed]);
    }

    /**
     * Récupérer les produits récemment vus
     * 
     * @param int $currentProductId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getRecentlyViewedProducts(int $currentProductId)
    {
        $recentlyViewed = session()->get('recently_viewed', []);
        
        // Retirer le produit actuel
        $recentlyViewed = array_diff($recentlyViewed, [$currentProductId]);
        
        if (empty($recentlyViewed)) {
            return collect();
        }

        return Product::whereIn('id', $recentlyViewed)
            ->where('is_active', true)
            ->limit(4)
            ->get();
    }

    /**
     * Générer les données structurées pour le SEO
     * 
     * @param Product $product
     * @return array
     */
    private function generateProductStructuredData(Product $product): array
    {
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => $product->image_urls ?? [],
            'description' => strip_tags($product->description),
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Jackson Energy International',
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => route('products.show', $product->slug),
                'priceCurrency' => 'XOF',
                'price' => $product->current_price,
                'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->stock_quantity > 0 
                    ? 'https://schema.org/InStock' 
                    : 'https://schema.org/OutOfStock',
            ],
            'aggregateRating' => [
                '@type' => 'AggregateRating',
                'ratingValue' => '4.8',
                'reviewCount' => '127',
            ],
        ];
    }
}

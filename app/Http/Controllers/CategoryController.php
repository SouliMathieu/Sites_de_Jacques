<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Afficher toutes les catégories actives
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mise en cache pour optimiser les performances
        $categories = Cache::remember('categories.index', 3600, function () {
            return Category::where('is_active', true)
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });

        // SEO dynamique
        $pageTitle = 'Toutes nos catégories - Jackson Energy International';
        $pageDescription = 'Découvrez toutes nos catégories de produits solaires : panneaux solaires, batteries, onduleurs, kits complets et accessoires au Burkina Faso.';

        return view('categories.index', compact('categories', 'pageTitle', 'pageDescription'));
    }

    /**
     * Afficher une catégorie avec ses produits (avec filtres et tri)
     * 
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show($slug, Request $request)
    {
        // Récupérer la catégorie
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->firstOrFail();

        // Construction de la requête avec Query Builder
        $query = $category->products()
            ->where('is_active', true)
            ->with(['category']);

        // 🔍 FILTRES
        
        // Filtre par prix
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Filtre par disponibilité
        if ($request->filled('in_stock') && $request->in_stock == '1') {
            $query->where('stock_quantity', '>', 0);
        }

        // Filtre par promotion
        if ($request->filled('on_sale') && $request->on_sale == '1') {
            $query->whereNotNull('promotional_price');
        }

        // Filtre par produits vedettes
        if ($request->filled('featured') && $request->featured == '1') {
            $query->where('is_featured', true);
        }

        // Recherche dans la catégorie
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('specifications', 'like', "%{$searchTerm}%");
            });
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
                // Tri par nombre de ventes (si vous avez ce champ)
                // $query->orderBy('sales_count', 'desc');
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('views_count', 'desc');
                break;
            
            case 'on_sale':
                $query->whereNotNull('promotional_price')
                      ->orderByRaw('((price - promotional_price) / price * 100) DESC');
                break;
            
            default: // 'default'
                $query->orderBy('is_featured', 'desc')
                      ->orderBy('sort_order')
                      ->latest();
                break;
        }

        // Pagination avec paramètres
        $perPage = $request->input('per_page', 12);
        $perPage = in_array($perPage, [12, 24, 36, 48]) ? $perPage : 12;
        
        $products = $query->paginate($perPage)->withQueryString();

        // Statistiques pour la sidebar
        $stats = [
            'total_products' => $category->products()->where('is_active', true)->count(),
            'in_stock' => $category->products()->where('is_active', true)->where('stock_quantity', '>', 0)->count(),
            'on_sale' => $category->products()->where('is_active', true)->whereNotNull('promotional_price')->count(),
            'price_range' => [
                'min' => $category->products()->where('is_active', true)->min('price'),
                'max' => $category->products()->where('is_active', true)->max('price'),
            ],
        ];

        // Catégories connexes (même niveau)
        $relatedCategories = Category::where('is_active', true)
            ->where('id', '!=', $category->id)
            ->withCount(['products' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Produits vedettes de la catégorie
        $featuredProducts = $category->products()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->where('stock_quantity', '>', 0)
            ->limit(4)
            ->get();

        // SEO dynamique
        $pageTitle = $category->meta_title ?: $category->name . ' - Jackson Energy International';
        $pageDescription = $category->meta_description ?: 'Découvrez notre sélection de ' . strtolower($category->name) . ' de qualité supérieure au Burkina Faso. ' . $category->products_count . ' produits disponibles.';
        $pageKeywords = $category->meta_keywords ?: $category->name . ', énergie solaire, Burkina Faso, Ouagadougou';

        // Breadcrumbs
        $breadcrumbs = [
            ['name' => 'Accueil', 'url' => route('home')],
            ['name' => 'Catégories', 'url' => route('categories.index')],
            ['name' => $category->name, 'url' => null],
        ];

        return view('categories.show', compact(
            'category',
            'products',
            'stats',
            'relatedCategories',
            'featuredProducts',
            'sortBy',
            'perPage',
            'pageTitle',
            'pageDescription',
            'pageKeywords',
            'breadcrumbs'
        ));
    }

    /**
     * Recherche rapide dans une catégorie (AJAX)
     * 
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($slug, Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $searchTerm = $request->input('q');

        $products = $category->products()
            ->where('is_active', true)
            ->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
            })
            ->select('id', 'name', 'slug', 'price', 'promotional_price', 'stock_quantity')
            ->limit(10)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'url' => route('products.show', $product->slug),
                    'price' => number_format($product->current_price, 0, ',', ' ') . ' FCFA',
                    'in_stock' => $product->stock_quantity > 0,
                ];
            });

        return response()->json([
            'success' => true,
            'results' => $products,
            'count' => $products->count(),
        ]);
    }

    /**
     * Obtenir les options de filtrage pour une catégorie (AJAX)
     * 
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function filterOptions($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $priceRange = $category->products()
            ->where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $options = [
            'price_range' => [
                'min' => (int) $priceRange->min_price,
                'max' => (int) $priceRange->max_price,
            ],
            'availability' => [
                'in_stock' => $category->products()->where('is_active', true)->where('stock_quantity', '>', 0)->count(),
                'out_of_stock' => $category->products()->where('is_active', true)->where('stock_quantity', '<=', 0)->count(),
            ],
            'promotions' => [
                'on_sale' => $category->products()->where('is_active', true)->whereNotNull('promotional_price')->count(),
            ],
            'features' => [
                'featured' => $category->products()->where('is_active', true)->where('is_featured', true)->count(),
            ],
        ];

        return response()->json([
            'success' => true,
            'options' => $options,
        ]);
    }

    /**
     * Comparer plusieurs produits d'une catégorie
     * 
     * @param string $slug
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function compare($slug, Request $request)
    {
        $request->validate([
            'products' => 'required|array|min:2|max:4',
            'products.*' => 'required|integer|exists:products,id',
        ]);

        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = Product::whereIn('id', $request->products)
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->with('category')
            ->get();

        if ($products->count() < 2) {
            return redirect()->route('categories.show', $slug)
                ->with('error', 'Veuillez sélectionner au moins 2 produits à comparer.');
        }

        return view('categories.compare', compact('category', 'products'));
    }
}

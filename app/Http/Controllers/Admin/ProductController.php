<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Afficher la liste des produits
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('specifications', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtres par statut
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                case 'on_sale':
                    $query->whereNotNull('promotional_price');
                    break;
                case 'in_stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('stock_quantity', '=', 0);
                    break;
                case 'low_stock':
                    $query->whereBetween('stock_quantity', [1, 9]);
                    break;
                case 'with_images':
                    $query->withImages();
                    break;
                case 'with_videos':
                    $query->withVideos();
                    break;
            }
        }

        // Tri
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        
        if (in_array($sortBy, ['name', 'price', 'stock_quantity', 'created_at', 'views_count'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage)->withQueryString();

        // Catégories pour le filtre
        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        // Statistiques
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'featured' => Product::where('is_featured', true)->count(),
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'low_stock' => Product::whereBetween('stock_quantity', [1, 9])->count(),
            'on_sale' => Product::whereNotNull('promotional_price')->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Enregistrer un nouveau produit
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'required|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'warranty' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'videos.*' => 'nullable|mimes:mp4,mov,avi,webm|max:51200',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Le nom du produit est obligatoire.',
            'name.unique' => 'Un produit avec ce nom existe déjà.',
            'price.required' => 'Le prix est obligatoire.',
            'promotional_price.lt' => 'Le prix promotionnel doit être inférieur au prix normal.',
            'category_id.required' => 'Veuillez sélectionner une catégorie.',
            'images.*.image' => 'Les fichiers doivent être des images.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 2 Mo.',
            'videos.*.max' => 'Chaque vidéo ne doit pas dépasser 50 Mo.',
        ]);

        DB::beginTransaction();

        try {
            // Génération du slug unique
            $slug = $validated['slug'] ?? Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (Product::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            Log::info('Création produit - Début', [
                'name' => $validated['name'],
                'has_images' => $request->hasFile('images'),
                'has_videos' => $request->hasFile('videos'),
            ]);

            // Upload des images
            $imageNames = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imageName = time() . '_' . ($index + 1) . '_' . Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products/images', $imageName, 'public');
                    $imageNames[] = $imageName;
                    
                    Log::info('Image uploadée', ['filename' => $imageName]);
                }
            }

            // Upload des vidéos
            $videoNames = [];
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $index => $video) {
                    $videoName = time() . '_video_' . ($index + 1) . '_' . Str::slug($validated['name']) . '.' . $video->getClientOriginalExtension();
                    $video->storeAs('products/videos', $videoName, 'public');
                    $videoNames[] = $videoName;
                    
                    Log::info('Vidéo uploadée', ['filename' => $videoName]);
                }
            }

            // Création du produit
            $product = Product::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'],
                'specifications' => $validated['specifications'] ?? null,
                'price' => $validated['price'],
                'promotional_price' => $validated['promotional_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'warranty' => $validated['warranty'] ?? null,
                'category_id' => $validated['category_id'],
                'images' => $imageNames,
                'videos' => $videoNames,
                'is_featured' => $request->boolean('is_featured', false),
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $validated['sort_order'] ?? 0,
                'meta_title' => $validated['meta_title'] ?? $validated['name'],
                'meta_description' => $validated['meta_description'] ?? Str::limit(strip_tags($validated['description']), 160),
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            DB::commit();

            // Vider le cache
            Cache::forget('products.featured.random');

            Log::info('Produit créé avec succès', [
                'product_id' => $product->id,
                'name' => $product->name,
                'images_count' => count($imageNames),
                'videos_count' => count($videoNames),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Produit \"{$product->name}\" créé avec succès ! ({$product->images_count} image(s), {$product->videos_count} vidéo(s))");

        } catch (\Exception $e) {
            DB::rollBack();

            // Supprimer les fichiers uploadés en cas d'erreur
            foreach ($imageNames ?? [] as $imageName) {
                Storage::disk('public')->delete("products/images/{$imageName}");
            }
            foreach ($videoNames ?? [] as $videoName) {
                Storage::disk('public')->delete("products/videos/{$videoName}");
            }

            Log::error('Erreur création produit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du produit : ' . $e->getMessage());
        }
    }

    /**
     * Afficher un produit
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        $product->load(['category', 'orderItems.order']);

        // Statistiques du produit
        $stats = [
            'total_sold' => $product->orderItems()->sum('quantity'),
            'total_revenue' => $product->orderItems()->sum('total_price'),
            'orders_count' => $product->orderItems()->distinct('order_id')->count('order_id'),
            'views_count' => $product->views_count ?? 0,
        ];

        return view('admin.products.show', compact('product', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     *
     * @param Product $product
     * @return \Illuminate\View\View
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Mettre à jour un produit
     *
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products')->ignore($product->id)],
            'description' => 'required|string',
            'specifications' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'promotional_price' => 'nullable|numeric|min:0|lt:price',
            'stock_quantity' => 'required|integer|min:0',
            'warranty' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'videos.*' => 'nullable|mimes:mp4,mov,avi,webm|max:51200',
            'remove_images' => 'nullable|array',
            'remove_images.*' => 'integer',
            'remove_videos' => 'nullable|array',
            'remove_videos.*' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Générer le slug si le nom a changé
            $slug = $product->slug;
            if ($product->name !== $validated['name']) {
                $slug = $validated['slug'] ?? Str::slug($validated['name']);
                $originalSlug = $slug;
                $counter = 1;

                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Supprimer les images sélectionnées
            if ($request->filled('remove_images')) {
                $currentImages = $product->images ?? [];
                foreach ($request->remove_images as $index) {
                    if (isset($currentImages[$index])) {
                        $imagePath = $currentImages[$index];
                        Storage::disk('public')->delete("products/images/{$imagePath}");
                        unset($currentImages[$index]);
                        Log::info('Image supprimée', ['filename' => $imagePath]);
                    }
                }
                $product->update(['images' => array_values($currentImages)]);
                $product->refresh();
            }

            // Supprimer les vidéos sélectionnées
            if ($request->filled('remove_videos')) {
                $currentVideos = $product->videos ?? [];
                foreach ($request->remove_videos as $index) {
                    if (isset($currentVideos[$index])) {
                        $videoPath = $currentVideos[$index];
                        Storage::disk('public')->delete("products/videos/{$videoPath}");
                        unset($currentVideos[$index]);
                        Log::info('Vidéo supprimée', ['filename' => $videoPath]);
                    }
                }
                $product->update(['videos' => array_values($currentVideos)]);
                $product->refresh();
            }

            // Ajouter de nouvelles images
            if ($request->hasFile('images')) {
                $currentImages = $product->images ?? [];
                foreach ($request->file('images') as $index => $image) {
                    $imageName = time() . '_' . (count($currentImages) + $index + 1) . '_' . Str::slug($validated['name']) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products/images', $imageName, 'public');
                    $currentImages[] = $imageName;
                    Log::info('Nouvelle image ajoutée', ['filename' => $imageName]);
                }
                $product->update(['images' => $currentImages]);
                $product->refresh();
            }

            // Ajouter de nouvelles vidéos
            if ($request->hasFile('videos')) {
                $currentVideos = $product->videos ?? [];
                foreach ($request->file('videos') as $index => $video) {
                    $videoName = time() . '_video_' . (count($currentVideos) + $index + 1) . '_' . Str::slug($validated['name']) . '.' . $video->getClientOriginalExtension();
                    $video->storeAs('products/videos', $videoName, 'public');
                    $currentVideos[] = $videoName;
                    Log::info('Nouvelle vidéo ajoutée', ['filename' => $videoName]);
                }
                $product->update(['videos' => $currentVideos]);
                $product->refresh();
            }

            // Mettre à jour les autres données
            $product->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'],
                'specifications' => $validated['specifications'] ?? null,
                'price' => $validated['price'],
                'promotional_price' => $validated['promotional_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'warranty' => $validated['warranty'] ?? null,
                'category_id' => $validated['category_id'],
                'is_featured' => $request->boolean('is_featured', false),
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $validated['sort_order'] ?? 0,
                'meta_title' => $validated['meta_title'] ?? $validated['name'],
                'meta_description' => $validated['meta_description'] ?? Str::limit(strip_tags($validated['description']), 160),
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            DB::commit();

            Cache::forget('products.featured.random');

            Log::info('Produit mis à jour', [
                'product_id' => $product->id,
                'name' => $product->name,
                'images_count' => $product->images_count,
                'videos_count' => $product->videos_count,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Produit \"{$product->name}\" mis à jour avec succès ! ({$product->images_count} image(s), {$product->videos_count} vidéo(s))");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur mise à jour produit', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un produit
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        // Vérifier les commandes liées
        $orderItemsCount = $product->orderItems()->count();
        if ($orderItemsCount > 0) {
            return back()->with('error', "Impossible de supprimer ce produit car il est lié à {$orderItemsCount} commande(s).");
        }

        try {
            $productName = $product->name;
            $imagesCount = $product->images_count;
            $videosCount = $product->videos_count;

            // Supprimer les médias
            $product->deleteAllMedia();

            // Supprimer le produit
            $product->delete();

            Cache::forget('products.featured.random');

            Log::info('Produit supprimé', [
                'name' => $productName,
                'images_deleted' => $imagesCount,
                'videos_deleted' => $videosCount,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Produit \"{$productName}\" supprimé avec succès !");

        } catch (\Exception $e) {
            Log::error('Erreur suppression produit', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Basculer le statut actif/inactif
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Product $product)
    {
        $product->toggleStatus();
        Cache::forget('products.featured.random');

        $status = $product->is_active ? 'activé' : 'désactivé';
        return back()->with('success', "Produit {$status} avec succès !");
    }

    /**
     * Dupliquer un produit
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate(Product $product)
    {
        try {
            $newProduct = $product->duplicate();

            return redirect()
                ->route('admin.products.edit', $newProduct)
                ->with('success', 'Produit dupliqué avec succès ! Modifiez les informations.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la duplication.');
        }
    }

    /**
     * Export des produits en CSV
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $products = Product::with('category')->orderBy('name')->get();

        $csv = "ID,Nom,Catégorie,Prix,Prix promo,Stock,Statut,Vedette,Créé le\n";

        foreach ($products as $product) {
            $csv .= implode(',', [
                $product->id,
                '"' . $product->name . '"',
                '"' . $product->category->name . '"',
                $product->price,
                $product->promotional_price ?? '',
                $product->stock_quantity,
                $product->is_active ? 'Actif' : 'Inactif',
                $product->is_featured ? 'Oui' : 'Non',
                $product->created_at->format('d/m/Y H:i'),
            ]) . "\n";
        }

        $filename = 'produits_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}

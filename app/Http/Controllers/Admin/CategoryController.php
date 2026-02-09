<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Afficher la liste des catégories
     */
    public function index(Request $request)
    {
        $query = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }]);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filtre par vedette
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        // Tri
        $sortBy = $request->input('sort', 'sort_order');
        $sortOrder = $request->input('order', 'asc');
        
        if (in_array($sortBy, ['name', 'created_at', 'products_count', 'sort_order'])) {
            if ($sortBy === 'products_count') {
                $query->orderBy('products_count', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }

        $perPage = $request->input('per_page', 15);
        $categories = $query->paginate($perPage)->withQueryString();

        // Statistiques
        $stats = [
            'total' => Category::count(),
            'active' => Category::where('is_active', true)->count(),
            'inactive' => Category::where('is_active', false)->count(),
            'featured' => Category::where('is_featured', true)->count(),
            'with_products' => Category::has('products')->count(),
        ];

        return view('admin.categories.index', compact('categories', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Enregistrer une nouvelle catégorie
     * 
     * ✅ CORRECTION: Gère image_file ET image_url
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',  // ✅ image_file
            'image_url' => 'nullable|url|max:500',  // ✅ image_url
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
            'show_in_menu' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Une catégorie avec ce nom existe déjà.',
            'image_file.image' => 'Le fichier doit être une image.',
            'image_file.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'image_url.url' => 'L\'URL de l\'image n\'est pas valide.',
        ]);

        DB::beginTransaction();

        try {
            // ✅ CORRECTION: Gérer image_file OU image_url
            $imagePath = null;
            
            if ($request->hasFile('image_file')) {
                // Upload du fichier
                $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $request->file('image_file')->getClientOriginalExtension();
                $request->file('image_file')->storeAs('categories', $imageName, 'public');
                $imagePath = $imageName;
                
                Log::info('Image de catégorie uploadée', ['filename' => $imageName]);
            } elseif ($request->filled('image_url')) {
                // Utiliser l'URL directement
                $imagePath = $validated['image_url'];
                
                Log::info('URL d\'image de catégorie utilisée', ['url' => $imagePath]);
            }

            // Créer la catégorie
            $category = Category::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
                'icon' => $validated['icon'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'show_in_menu' => $request->boolean('show_in_menu', true),
                'is_featured' => $request->boolean('is_featured', false),
                'meta_title' => $validated['meta_title'] ?? $validated['name'],
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            DB::commit();

            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            Log::info("Catégorie créée", [
                'category_id' => $category->id,
                'name' => $category->name,
                'has_image' => !empty($imagePath),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Catégorie créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            // Supprimer l'image uploadée si erreur
            if ($imagePath && !filter_var($imagePath, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete('categories/' . $imagePath);
            }

            Log::error("Erreur création catégorie", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Afficher une catégorie
     */
    public function show(Category $category)
    {
        $category->loadCount('products');

        $products = $category->products()
            ->with('category')
            ->latest()
            ->paginate(12);

        $stats = [
            'total_products' => $category->products()->count(),
            'active_products' => $category->products()->where('is_active', true)->count(),
            'in_stock' => $category->products()->where('stock_quantity', '>', 0)->count(),
            'on_sale' => $category->products()->whereNotNull('promotional_price')->count(),
            'total_value' => $category->products()->sum('price'),
        ];

        return view('admin.categories.show', compact('category', 'products', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::where('is_active', true)
            ->where('id', '!=', $category->id)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Mettre à jour une catégorie
     * 
     * ✅ CORRECTION: Gère image_file ET image_url + suppression
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string|max:1000',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',  // ✅ image_file
            'image_url' => 'nullable|url|max:500',  // ✅ image_url
            'remove_image' => 'boolean',  // ✅ Option de suppression
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
            'show_in_menu' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Une catégorie avec ce nom existe déjà.',
            'image_file.image' => 'Le fichier doit être une image.',
            'image_file.max' => 'L\'image ne doit pas dépasser 2 Mo.',
            'image_url.url' => 'L\'URL de l\'image n\'est pas valide.',
        ]);

        DB::beginTransaction();

        try {
            // ✅ CORRECTION: Gérer l'image correctement
            $imagePath = $category->image;
            
            // Supprimer l'image si demandé
            if ($request->boolean('remove_image')) {
                if ($category->image && !filter_var($category->image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete('categories/' . $category->image);
                }
                $imagePath = null;
                
                Log::info('Image de catégorie supprimée', [
                    'category_id' => $category->id,
                ]);
            }
            
            // Upload nouvelle image
            if ($request->hasFile('image_file')) {
                // Supprimer l'ancienne si ce n'est pas une URL
                if ($category->image && !filter_var($category->image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete('categories/' . $category->image);
                }
                
                $imageName = time() . '_' . Str::slug($validated['name']) . '.' . $request->file('image_file')->getClientOriginalExtension();
                $request->file('image_file')->storeAs('categories', $imageName, 'public');
                $imagePath = $imageName;
                
                Log::info('Nouvelle image de catégorie uploadée', ['filename' => $imageName]);
            } elseif ($request->filled('image_url') && !$request->boolean('remove_image')) {
                // Supprimer l'ancienne image fichier si on passe à une URL
                if ($category->image && !filter_var($category->image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete('categories/' . $category->image);
                }
                
                $imagePath = $validated['image_url'];
                
                Log::info('URL d\'image de catégorie utilisée', ['url' => $imagePath]);
            }

            // Mettre à jour la catégorie
            $category->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'] ?? Str::slug($validated['name']),
                'description' => $validated['description'] ?? null,
                'image' => $imagePath,
                'icon' => $validated['icon'] ?? null,
                'parent_id' => $validated['parent_id'] ?? null,
                'sort_order' => $validated['sort_order'] ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'show_in_menu' => $request->boolean('show_in_menu', true),
                'is_featured' => $request->boolean('is_featured', false),
                'meta_title' => $validated['meta_title'] ?? $validated['name'],
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            DB::commit();

            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            Log::info("Catégorie mise à jour", [
                'category_id' => $category->id,
                'name' => $category->name,
                'image_changed' => $category->wasChanged('image'),
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Catégorie mise à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erreur mise à jour catégorie", [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie qui contient des produits.');
        }

        if ($category->children()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie qui contient des sous-catégories.');
        }

        try {
            $categoryName = $category->name;

            // Supprimer l'image si ce n'est pas une URL
            if ($category->image && !filter_var($category->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete('categories/' . $category->image);
            }

            $category->delete();

            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            Log::info("Catégorie supprimée", [
                'category_name' => $categoryName,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Catégorie supprimée avec succès !');

        } catch (\Exception $e) {
            Log::error("Erreur suppression catégorie", [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Basculer le statut actif/inactif
     */
    public function toggleStatus(Category $category)
    {
        try {
            $category->toggleStatus();
            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            $status = $category->is_active ? 'activée' : 'désactivée';
            return back()->with('success', "Catégorie {$status} avec succès !");

        } catch (\Exception $e) {
            Log::error("Erreur toggle statut catégorie", [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Dupliquer une catégorie
     */
    public function duplicate(Category $category)
    {
        try {
            $newCategory = $category->duplicate();
            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            Log::info("Catégorie dupliquée", [
                'original_id' => $category->id,
                'new_id' => $newCategory->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.categories.edit', $newCategory)
                ->with('success', 'Catégorie dupliquée avec succès !');

        } catch (\Exception $e) {
            Log::error("Erreur duplication catégorie", [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la duplication.');
        }
    }

    /**
     * Export des catégories en CSV
     */
    public function export()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();

        $csv = "ID,Nom,Slug,Produits,Statut,Vedette,Ordre,Créé le\n";

        foreach ($categories as $category) {
            $csv .= implode(',', [
                $category->id,
                '"' . $category->name . '"',
                $category->slug,
                $category->products_count,
                $category->is_active ? 'Actif' : 'Inactif',
                $category->is_featured ? 'Oui' : 'Non',
                $category->sort_order,
                $category->created_at->format('d/m/Y H:i'),
            ]) . "\n";
        }

        $filename = 'categories_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
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
     *
     * @param Request $request
     * @return \Illuminate\View\View
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
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Catégories parentes pour hiérarchie (si vous l'utilisez)
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Enregistrer une nouvelle catégorie
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Une catégorie avec ce nom existe déjà.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        DB::beginTransaction();

        try {
            // Upload de l'image
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
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
                'is_featured' => $request->boolean('is_featured', false),
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            DB::commit();

            // Vider le cache
            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            Log::info("Catégorie créée", [
                'category_id' => $category->id,
                'name' => $category->name,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Catégorie créée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();

            // Supprimer l'image si uploadée
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            Log::error("Erreur création catégorie", [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de la catégorie.');
        }
    }

    /**
     * Afficher une catégorie
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category)
    {
        $category->loadCount('products');

        // Produits de la catégorie avec pagination
        $products = $category->products()
            ->with('category')
            ->latest()
            ->paginate(12);

        // Statistiques de la catégorie
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
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        // Catégories parentes (exclure la catégorie actuelle et ses enfants)
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
     * @param Request $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Le nom de la catégorie est obligatoire.',
            'name.unique' => 'Une catégorie avec ce nom existe déjà.',
            'image.image' => 'Le fichier doit être une image.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        DB::beginTransaction();

        try {
            // Upload de la nouvelle image
            $imagePath = $category->image;
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }

                $imagePath = $request->file('image')->store('categories', 'public');
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
                'is_featured' => $request->boolean('is_featured', false),
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'meta_keywords' => $validated['meta_keywords'] ?? null,
            ]);

            DB::commit();

            // Vider le cache
            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            Log::info("Catégorie mise à jour", [
                'category_id' => $category->id,
                'name' => $category->name,
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
                'user_id' => auth()->id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour de la catégorie.');
        }
    }

    /**
     * Supprimer une catégorie
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Vérifier si la catégorie a des produits
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie qui contient des produits. Veuillez d\'abord supprimer ou déplacer les produits.');
        }

        // Vérifier si la catégorie a des sous-catégories
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer une catégorie qui contient des sous-catégories.');
        }

        try {
            $categoryName = $category->name;

            // Supprimer l'image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            // Supprimer la catégorie
            $category->delete();

            // Vider le cache
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
                'user_id' => auth()->id(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression de la catégorie.');
        }
    }

    /**
     * Basculer le statut actif/inactif
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
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
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
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
                ->with('success', 'Catégorie dupliquée avec succès ! Veuillez modifier les informations.');

        } catch (\Exception $e) {
            Log::error("Erreur duplication catégorie", [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la duplication.');
        }
    }

    /**
     * Supprimer l'image d'une catégorie
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteImage(Category $category)
    {
        try {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->update(['image' => null]);

            return back()->with('success', 'Image supprimée avec succès !');

        } catch (\Exception $e) {
            Log::error("Erreur suppression image catégorie", [
                'category_id' => $category->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Réorganiser les catégories (drag & drop)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->categories as $categoryData) {
                Category::where('id', $categoryData['id'])
                    ->update(['sort_order' => $categoryData['sort_order']]);
            }

            DB::commit();

            Cache::forget('categories.index');
            Cache::forget('categories.with_counts');

            return response()->json([
                'success' => true,
                'message' => 'Ordre des catégories mis à jour avec succès !',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Erreur réorganisation catégories", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    /**
     * Export des catégories en CSV
     *
     * @return \Illuminate\Http\Response
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
   public function index(Request $request)
{
    $query = Product::with('category');

    // Recherche par nom
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
    }

    // Filtrage par catégorie
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // Filtrage par statut
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
        }
    }

    $products = $query->latest()->paginate(15)->withQueryString();

    return view('admin.products.index', compact('products'));
}


    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try {
            // Validation des données
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'promotional_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'specifications' => 'nullable|string',
                'warranty' => 'nullable|string',
                'images' => 'nullable|string',
                'videos' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            // Logs pour déboguer
            Log::info('Création produit - Données reçues', [
                'name' => $request->name,
                'images_raw' => $request->images,
                'videos_raw' => $request->videos,
                'category_id' => $request->category_id,
                'price' => $request->price
            ]);

            // Génération d'un slug unique
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;

            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Traitement des images
            $images = [];
            if ($request->filled('images')) {
                $imageUrls = explode(',', $request->images);
                foreach ($imageUrls as $url) {
                    $cleanUrl = trim($url);
                    if (!empty($cleanUrl)) {
                        $images[] = $cleanUrl;
                    }
                }
            }

            // Traitement des vidéos
            $videos = [];
            if ($request->filled('videos')) {
                $videoUrls = explode(',', $request->videos);
                foreach ($videoUrls as $url) {
                    $cleanUrl = trim($url);
                    if (!empty($cleanUrl)) {
                        $videos[] = $cleanUrl;
                    }
                }
            }

            // Log des données finales
            Log::info('Création produit - Données finales', [
                'slug' => $slug,
                'images_processed' => $images,
                'videos_processed' => $videos,
                'images_count' => count($images),
                'videos_count' => count($videos)
            ]);

            // Création du produit
            $product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'specifications' => $request->specifications,
                'price' => $request->price,
                'promotional_price' => $request->promotional_price,
                'stock_quantity' => $request->stock_quantity,
                'images' => $images,
                'videos' => $videos,
                'warranty' => $request->warranty,
                'category_id' => $request->category_id,
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
                'meta_title' => $request->meta_title ?: $request->name,
                'meta_description' => $request->meta_description ?: Str::limit($request->description, 160),
            ]);

            // Log du produit créé
            Log::info('Produit créé avec succès', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'images_saved' => $product->images,
                'videos_saved' => $product->videos,
                'category' => $product->category_id
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit "' . $product->name . '" créé avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation lors de la création du produit', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);

            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du produit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la création du produit : ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            // Validation des données
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'promotional_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'specifications' => 'nullable|string',
                'warranty' => 'nullable|string',
                'images' => 'nullable|string',
                'videos' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            // Génération d'un slug unique (si le nom a changé)
            $slug = $product->slug;
            if ($product->name !== $request->name) {
                $baseSlug = Str::slug($request->name);
                $slug = $baseSlug;
                $counter = 1;

                while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Traitement des images
            $images = [];
            if ($request->filled('images')) {
                $imageUrls = explode(',', $request->images);
                foreach ($imageUrls as $url) {
                    $cleanUrl = trim($url);
                    if (!empty($cleanUrl)) {
                        $images[] = $cleanUrl;
                    }
                }
            }

            // Traitement des vidéos
            $videos = [];
            if ($request->filled('videos')) {
                $videoUrls = explode(',', $request->videos);
                foreach ($videoUrls as $url) {
                    $cleanUrl = trim($url);
                    if (!empty($cleanUrl)) {
                        $videos[] = $cleanUrl;
                    }
                }
            }

            // Mise à jour du produit
            $product->update([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'specifications' => $request->specifications,
                'price' => $request->price,
                'promotional_price' => $request->promotional_price,
                'stock_quantity' => $request->stock_quantity,
                'images' => $images,
                'videos' => $videos,
                'warranty' => $request->warranty,
                'category_id' => $request->category_id,
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
                'meta_title' => $request->meta_title ?: $request->name,
                'meta_description' => $request->meta_description ?: Str::limit($request->description, 160),
            ]);

            Log::info('Produit mis à jour avec succès', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'images_count' => count($images),
                'videos_count' => count($videos)
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit "' . $product->name . '" mis à jour avec succès !');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du produit', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->back()
                ->with('error', 'Erreur lors de la mise à jour du produit : ' . $e->getMessage())
                ->withInput();
        }
    }

 public function destroy(Product $product)
{
    try {
        $productName = $product->name;
        $blockingRelations = [];

        // Vérifier les relations qui pourraient bloquer
        $orderItemsCount = $product->orderItems()->count();
        if ($orderItemsCount > 0) {
            $blockingRelations[] = "Éléments de commande ({$orderItemsCount})";
        }

        // Si des relations bloquent la suppression
        if (!empty($blockingRelations)) {
            $message = 'Le produit "' . $productName . '" est lié à : ' . implode(', ', $blockingRelations) . '. ';
            $message .= 'Vous devez d\'abord supprimer les commandes concernées depuis la section "Commandes".';

            return redirect()
                ->route('admin.products.index')
                ->with('error', $message);
        }

        // Supprimer les fichiers physiques
        if ($product->images) {
            foreach ($product->images as $imagePath) {
                $fullPath = storage_path('app/public/' . $imagePath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        if ($product->videos) {
            foreach ($product->videos as $videoPath) {
                $fullPath = storage_path('app/public/' . $videoPath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }

        // Supprimer le produit
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produit "' . $productName . '" supprimé avec succès !');

    } catch (\Exception $e) {
        Log::error('Erreur lors de la suppression du produit', [
            'product_id' => $product->id,
            'error' => $e->getMessage()
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
    }
}


}

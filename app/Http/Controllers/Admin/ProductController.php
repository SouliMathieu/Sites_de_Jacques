<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Recherche
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
                case 'with_images':
                    $query->withImages();
                    break;
                case 'with_videos':
                    $query->withVideos();
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
            // ✅ VALIDATION pour upload multiple
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'promotional_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'specifications' => 'nullable|string',
                'warranty' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048', // ✅ Multiple images
                'videos.*' => 'nullable|mimes:mp4,mov,avi|max:20480', // ✅ Multiple vidéos
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
            ]);

            Log::info('Création produit - Données reçues', [
                'name' => $request->name,
                'has_images' => $request->hasFile('images'),
                'has_videos' => $request->hasFile('videos'),
                'images_count' => $request->hasFile('images') ? count($request->file('images')) : 0,
                'videos_count' => $request->hasFile('videos') ? count($request->file('videos')) : 0,
            ]);

            // Génération d'un slug unique
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // ✅ GESTION UPLOAD MULTIPLE IMAGES
            $imageNames = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $imageName = time() . '_' . ($index + 1) . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products/images', $imageName, 'public');
                    $imageNames[] = $imageName;
                }
            }

            // ✅ GESTION UPLOAD MULTIPLE VIDÉOS
            $videoNames = [];
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $index => $video) {
                    $videoName = time() . '_video_' . ($index + 1) . '_' . Str::slug($request->name) . '.' . $video->getClientOriginalExtension();
                    $video->storeAs('products/images', $videoName, 'public');
                    $videoNames[] = $videoName;
                }
            }

            // ✅ CRÉATION avec arrays d'images/vidéos
            $product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'specifications' => $request->specifications,
                'price' => $request->price,
                'promotional_price' => $request->promotional_price,
                'stock_quantity' => $request->stock_quantity,
                'images' => $imageNames, // ✅ Array de noms de fichiers
                'videos' => $videoNames, // ✅ Array de noms de fichiers
                'warranty' => $request->warranty,
                'category_id' => $request->category_id,
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
                'meta_title' => $request->meta_title ?: $request->name,
                'meta_description' => $request->meta_description ?: Str::limit($request->description, 160),
            ]);

            Log::info('Produit créé avec succès', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'images_saved' => $product->images,
                'videos_saved' => $product->videos,
                'images_count' => $product->images_count,
                'videos_count' => $product->videos_count,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit "' . $product->name . '" créé avec succès ! (' . $product->images_count . ' image(s), ' . $product->videos_count . ' vidéo(s))');

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
            // ✅ VALIDATION pour update multiple
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'promotional_price' => 'nullable|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'specifications' => 'nullable|string',
                'warranty' => 'nullable|string',
                'images.*' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                'videos.*' => 'nullable|mimes:mp4,mov,avi|max:20480',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'remove_images' => 'nullable|string', // IDs des images à supprimer
                'remove_videos' => 'nullable|string', // IDs des vidéos à supprimer
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

            // ✅ SUPPRESSION des images sélectionnées
            if ($request->filled('remove_images')) {
                $imagesToRemove = explode(',', $request->remove_images);
                foreach ($imagesToRemove as $imageIndex) {
                    if (isset($product->images[$imageIndex])) {
                        $product->removeImage($product->images[$imageIndex]);
                    }
                }
                // Recharger le produit après suppression
                $product->refresh();
            }

            // ✅ SUPPRESSION des vidéos sélectionnées
            if ($request->filled('remove_videos')) {
                $videosToRemove = explode(',', $request->remove_videos);
                foreach ($videosToRemove as $videoIndex) {
                    if (isset($product->videos[$videoIndex])) {
                        $product->removeVideo($product->videos[$videoIndex]);
                    }
                }
                // Recharger le produit après suppression
                $product->refresh();
            }

            // ✅ AJOUT de nouvelles images
            if ($request->hasFile('images')) {
                $existingImages = $product->images ?: [];
                foreach ($request->file('images') as $index => $image) {
                    $imageName = time() . '_' . (count($existingImages) + $index + 1) . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products/images', $imageName, 'public');
                    $product->addImage($imageName);
                }
                // Recharger le produit après ajout
                $product->refresh();
            }

            // ✅ AJOUT de nouvelles vidéos
            if ($request->hasFile('videos')) {
                $existingVideos = $product->videos ?: [];
                foreach ($request->file('videos') as $index => $video) {
                    $videoName = time() . '_video_' . (count($existingVideos) + $index + 1) . '_' . Str::slug($request->name) . '.' . $video->getClientOriginalExtension();
                    $video->storeAs('products/images', $videoName, 'public');
                    $product->addVideo($videoName);
                }
                // Recharger le produit après ajout
                $product->refresh();
            }

            // ✅ MISE À JOUR des autres données
            $updateData = [
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'specifications' => $request->specifications,
                'price' => $request->price,
                'promotional_price' => $request->promotional_price,
                'stock_quantity' => $request->stock_quantity,
                'warranty' => $request->warranty,
                'category_id' => $request->category_id,
                'is_featured' => $request->boolean('is_featured'),
                'is_active' => $request->boolean('is_active', true),
                'meta_title' => $request->meta_title ?: $request->name,
                'meta_description' => $request->meta_description ?: Str::limit($request->description, 160),
            ];

            $product->update($updateData);

            Log::info('Produit mis à jour avec succès', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'images_count' => $product->images_count,
                'videos_count' => $product->videos_count,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit "' . $product->name . '" mis à jour avec succès ! (' . $product->images_count . ' image(s), ' . $product->videos_count . ' vidéo(s))');

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

            // Vérifications de relations
            $orderItemsCount = $product->orderItems()->count();
            if ($orderItemsCount > 0) {
                return redirect()
                    ->route('admin.products.index')
                    ->with('error', 'Le produit "' . $productName . '" est lié à ' . $orderItemsCount . ' commande(s). Supprimez d\'abord les commandes concernées.');
            }

            // ✅ SUPPRESSION automatique de tous les fichiers via le modèle
            $imagesCount = $product->images_count;
            $videosCount = $product->videos_count;

            // Supprimer toutes les images
            if ($product->images) {
                foreach ($product->images as $imagePath) {
                    $fullPath = storage_path('app/public/products/images/' . $imagePath);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            // Supprimer toutes les vidéos
            if ($product->videos) {
                foreach ($product->videos as $videoPath) {
                    $fullPath = storage_path('app/public/products/images/' . $videoPath);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            // Supprimer le produit
            $product->delete();

            Log::info('Produit supprimé avec succès', [
                'product_name' => $productName,
                'images_deleted' => $imagesCount,
                'videos_deleted' => $videosCount,
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produit "' . $productName . '" supprimé avec succès ! (' . $imagesCount . ' image(s) et ' . $videosCount . ' vidéo(s) supprimée(s))');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du produit', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    // ✅ NOUVELLE MÉTHODE : Supprimer une image spécifique
    public function removeImage(Request $request, Product $product)
    {
        $imageIndex = $request->input('image_index');
        
        if (isset($product->images[$imageIndex])) {
            $imagePath = $product->images[$imageIndex];
            $product->removeImage($imagePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Image supprimée avec succès'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Image non trouvée'
        ]);
    }

    // ✅ NOUVELLE MÉTHODE : Supprimer une vidéo spécifique
    public function removeVideo(Request $request, Product $product)
    {
        $videoIndex = $request->input('video_index');
        
        if (isset($product->videos[$videoIndex])) {
            $videoPath = $product->videos[$videoIndex];
            $product->removeVideo($videoPath);
            
            return response()->json([
                'success' => true,
                'message' => 'Vidéo supprimée avec succès'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Vidéo non trouvée'
        ]);
    }
}

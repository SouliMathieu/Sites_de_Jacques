@extends('admin.layouts.app')

@section('title', 'Ajouter un produit - Administration')

@section('content')
<div class="admin-form-bf">
    <div class="mb-8">
        <h1 class="text-3xl font-montserrat font-bold text-gray-900">Ajouter un produit</h1>
        <p class="text-gray-600">Créez un nouveau produit pour votre catalogue</p>
    </div>

    @if(session('success'))
        <div class="admin-message-success mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="admin-message-error mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Nom du produit *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="category_id" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
                    <select id="category_id" name="category_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('category_id') border-red-500 @enderror">
                        <option value="">Sélectionnez une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Description *</label>
                <textarea id="description" name="description" rows="4" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="specifications" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Spécifications techniques</label>
                <textarea id="specifications" name="specifications" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('specifications') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="price" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Prix (FCFA) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="1" required
                        class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="promotional_price" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Prix promotionnel (FCFA)</label>
                    <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price') }}" min="0" step="1"
                        class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                </div>
                
                <div>
                    <label for="stock_quantity" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Quantité en stock *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required
                        class="w-full px-4 admin-input-bf py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="warranty" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Garantie</label>
                <input type="text" id="warranty" name="warranty" value="{{ old('warranty') }}" placeholder="Ex: 2 ans"
                    class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
            </div>

            <!-- ✅ SECTION UPLOAD MULTIPLE CORRIGÉE -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📸 Images et vidéos du produit</h3>
                
                <!-- Upload multiple images -->
                <div class="mb-6">
                    <label for="images" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">
                        Images (Formats: JPG, PNG, GIF - Max: 2MB chacune)
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-vert-energie transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-vert-energie hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-vert-energie">
                                    <span>Télécharger des images</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">JPG, PNG, GIF jusqu'à 2MB chacune</p>
                        </div>
                    </div>
                    @error('images.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <!-- Prévisualisation des images -->
                    <div id="images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>

                <!-- Upload multiple vidéos -->
                <div class="mb-4">
                    <label for="videos" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">
                        Vidéos (Formats: MP4, MOV, AVI - Max: 20MB chacune)
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-vert-energie transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="videos" class="relative cursor-pointer bg-white rounded-md font-medium text-vert-energie hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-vert-energie">
                                    <span>Télécharger des vidéos</span>
                                    <input id="videos" name="videos[]" type="file" class="sr-only" multiple accept="video/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">MP4, MOV, AVI jusqu'à 20MB chacune</p>
                        </div>
                    </div>
                    @error('videos.*')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <!-- Prévisualisation des vidéos -->
                    <div id="videos-preview" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                </div>
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="flex items-center admin-label-bf">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded admin-input-bf border-gray-300 text-vert-energie shadow-sm focus:border-vert-energie focus:ring focus:ring-vert-energie focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Produit actif</span>
                </label>
                <label class="flex items-center admin-label-bf">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                        class="rounded admin-input-bf border-gray-300 text-vert-energie shadow-sm focus:border-vert-energie focus:ring focus:ring-vert-energie focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Produit vedette</span>
                </label>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔍 Optimisation SEO</h3>
                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Titre SEO</label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                            class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                    </div>
                    <div>
                        <label for="meta_description" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Description SEO</label>
                        <textarea id="meta_description" name="meta_description" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-6 border-t">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    ← Retour
                </a>
                <button type="submit" class="bg-vert-energie admin-btn-bf text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    💾 Créer le produit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation upload multiple...');

    // ✅ GESTION PRÉVISUALISATION IMAGES
    const imagesInput = document.getElementById('images');
    const imagesPreview = document.getElementById('images-preview');
    
    imagesInput.addEventListener('change', function(e) {
        imagesPreview.innerHTML = ''; // Vider les prévisualisations précédentes
        
        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg shadow">
                        <div class="absolute top-1 right-1 bg-red-500 text-white text-xs px-1 rounded cursor-pointer" 
                             onclick="removeImagePreview(this, ${index})">✕</div>
                        <p class="text-xs text-gray-600 mt-1 truncate">${file.name}</p>
                    `;
                    imagesPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
        
        console.log(`📸 ${e.target.files.length} image(s) sélectionnée(s)`);
    });

    // ✅ GESTION PRÉVISUALISATION VIDÉOS
    const videosInput = document.getElementById('videos');
    const videosPreview = document.getElementById('videos-preview');
    
    videosInput.addEventListener('change', function(e) {
        videosPreview.innerHTML = ''; // Vider les prévisualisations précédentes
        
        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('video/')) {
                const div = document.createElement('div');
                div.className = 'relative bg-gray-100 p-4 rounded-lg';
                div.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                            <p class="text-xs text-gray-600">${(file.size / 1024 / 1024).toFixed(1)} MB</p>
                        </div>
                        <div class="bg-red-500 text-white text-xs px-2 py-1 rounded cursor-pointer" 
                             onclick="removeVideoPreview(this, ${index})">Supprimer</div>
                    </div>
                `;
                videosPreview.appendChild(div);
            }
        });
        
        console.log(`🎥 ${e.target.files.length} vidéo(s) sélectionnée(s)`);
    });

    // ✅ FONCTIONS DE SUPPRESSION (globales)
    window.removeImagePreview = function(button, index) {
        button.closest('div').remove();
        // Note: Ne supprime pas réellement le fichier de l'input, juste l'aperçu
        console.log(`🗑️ Aperçu image ${index} supprimé`);
    };

    window.removeVideoPreview = function(button, index) {
        button.closest('div').remove();
        console.log(`🗑️ Aperçu vidéo ${index} supprimé`);
    };

    // ✅ VALIDATION AVANT SOUMISSION
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const images = imagesInput.files;
        const videos = videosInput.files;
        
        console.log(`📋 Soumission formulaire: ${images.length} image(s), ${videos.length} vidéo(s)`);
        
        // Validation des tailles de fichiers
        let hasError = false;
        
        Array.from(images).forEach(file => {
            if (file.size > 2 * 1024 * 1024) { // 2MB
                alert(`❌ L'image "${file.name}" est trop volumineuse (max 2MB)`);
                hasError = true;
            }
        });
        
        Array.from(videos).forEach(file => {
            if (file.size > 20 * 1024 * 1024) { // 20MB
                alert(`❌ La vidéo "${file.name}" est trop volumineuse (max 20MB)`);
                hasError = true;
            }
        });
        
        if (hasError) {
            e.preventDefault();
        }
    });
});
</script>
@endpush

@endsection

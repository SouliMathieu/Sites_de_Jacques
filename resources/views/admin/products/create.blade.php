@extends('admin.layouts.app')

@section('title', 'Ajouter un produit - Jackson Energy International')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-montserrat font-bold text-gray-900">➕ Ajouter un produit</h1>
                <p class="text-gray-600 mt-1">Créez un nouveau produit pour votre catalogue Jackson Energy</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                ← Retour à la liste
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf

            {{-- Informations de base --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                    Informations de base
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nom du produit <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('name') border-red-500 @enderror"
                            placeholder="Ex: Panneau solaire 300W">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Catégorie <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('category_id') border-red-500 @enderror">
                            <option value="">-- Sélectionnez une catégorie --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="5" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('description') border-red-500 @enderror"
                        placeholder="Décrivez le produit en détail...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1 flex items-center">
                            <span class="mr-1">⚠️</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="mt-6">
                    <label for="specifications" class="block text-sm font-semibold text-gray-700 mb-2">
                        Spécifications techniques
                    </label>
                    <textarea id="specifications" name="specifications" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        placeholder="Ex: Puissance: 300W, Voltage: 24V, Dimensions: 1640x992x40mm">{{ old('specifications') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">📋 Détails techniques du produit</p>
                </div>
            </div>

            {{-- Prix et stock --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                    Prix et stock
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                            Prix normal (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="1" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('price') border-red-500 @enderror"
                                placeholder="0">
                            <span class="absolute right-3 top-3 text-gray-400 font-medium">FCFA</span>
                        </div>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="promotional_price" class="block text-sm font-semibold text-gray-700 mb-2">
                            Prix promotionnel (FCFA)
                        </label>
                        <div class="relative">
                            <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price') }}" min="0" step="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                                placeholder="0">
                            <span class="absolute right-3 top-3 text-gray-400 font-medium">FCFA</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">🏷️ Optionnel - Prix en promotion</p>
                    </div>
                    
                    <div>
                        <label for="stock_quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                            Quantité en stock <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('stock_quantity') border-red-500 @enderror"
                            placeholder="0">
                        @error('stock_quantity')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="warranty" class="block text-sm font-semibold text-gray-700 mb-2">
                        Garantie
                    </label>
                    <input type="text" id="warranty" name="warranty" value="{{ old('warranty') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        placeholder="Ex: 2 ans, 12 mois, Garantie constructeur">
                    <p class="text-xs text-gray-500 mt-1">🛡️ Informations sur la garantie du produit</p>
                </div>
            </div>

            {{-- Médias --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                    Images et vidéos
                </h2>

                {{-- Images --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        📸 Images du produit
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition-colors bg-gray-50 hover:bg-green-50">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="images" class="relative cursor-pointer bg-white rounded-md font-semibold text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 px-3 py-1">
                                    <span>Choisir des images</span>
                                    <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">JPG, PNG, GIF jusqu'à 2MB par image</p>
                        </div>
                    </div>
                    @error('images.*')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <span class="mr-1">⚠️</span> {{ $message }}
                        </p>
                    @enderror
                    <div id="images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>

                {{-- Vidéos --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        🎥 Vidéos du produit
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition-colors bg-gray-50 hover:bg-green-50">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="videos" class="relative cursor-pointer bg-white rounded-md font-semibold text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 px-3 py-1">
                                    <span>Choisir des vidéos</span>
                                    <input id="videos" name="videos[]" type="file" class="sr-only" multiple accept="video/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">MP4, MOV, AVI jusqu'à 20MB par vidéo</p>
                        </div>
                    </div>
                    @error('videos.*')
                        <p class="text-red-500 text-sm mt-2 flex items-center">
                            <span class="mr-1">⚠️</span> {{ $message }}
                        </p>
                    @enderror
                    <div id="videos-preview" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                </div>
            </div>

            {{-- Options --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">4</span>
                    Options du produit
                </h2>
                
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            ✅ Produit actif (visible sur le site)
                        </span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            ⭐ Produit vedette (mis en avant)
                        </span>
                    </label>
                </div>
            </div>

            {{-- SEO --}}
            <div class="pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">5</span>
                    Optimisation SEO
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">
                            🏷️ Titre SEO
                        </label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" maxlength="60"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Titre optimisé pour les moteurs de recherche">
                        <p class="text-xs text-gray-500 mt-1">Recommandé: 50-60 caractères</p>
                    </div>
                    
                    <div>
                        <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">
                            📝 Description SEO
                        </label>
                        <textarea id="meta_description" name="meta_description" rows="3" maxlength="160"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                            placeholder="Description optimisée pour les moteurs de recherche">{{ old('meta_description') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Recommandé: 150-160 caractères</p>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t">
                <a href="{{ route('admin.products.index') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    ← Annuler
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                    💾 Créer le produit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation du formulaire de création de produit...');

    // Prévisualisation des images
    const imagesInput = document.getElementById('images');
    const imagesPreview = document.getElementById('images-preview');
    
    if (imagesInput && imagesPreview) {
        imagesInput.addEventListener('change', function(e) {
            imagesPreview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg shadow-md group-hover:shadow-lg transition">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition rounded-lg flex items-center justify-center">
                                <button type="button" onclick="removeImagePreview(this, ${index})" 
                                    class="opacity-0 group-hover:opacity-100 bg-red-500 text-white text-xs px-3 py-1 rounded-full transition">
                                    🗑️ Supprimer
                                </button>
                            </div>
                            <p class="text-xs text-gray-600 mt-2 truncate">${file.name}</p>
                            <p class="text-xs text-gray-400">${(file.size / 1024).toFixed(1)} KB</p>
                        `;
                        imagesPreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            console.log(`📸 ${e.target.files.length} image(s) sélectionnée(s)`);
        });
    }

    // Prévisualisation des vidéos
    const videosInput = document.getElementById('videos');
    const videosPreview = document.getElementById('videos-preview');
    
    if (videosInput && videosPreview) {
        videosInput.addEventListener('change', function(e) {
            videosPreview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                if (file.type.startsWith('video/')) {
                    const div = document.createElement('div');
                    div.className = 'relative bg-gradient-to-br from-gray-50 to-gray-100 p-4 rounded-lg shadow-md hover:shadow-lg transition group';
                    div.innerHTML = `
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-12 h-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">${file.name}</p>
                                <p class="text-xs text-gray-600 mt-1">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            </div>
                            <button type="button" onclick="removeVideoPreview(this, ${index})" 
                                class="flex-shrink-0 bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-full transition">
                                🗑️ Supprimer
                            </button>
                        </div>
                    `;
                    videosPreview.appendChild(div);
                }
            });
            
            console.log(`🎥 ${e.target.files.length} vidéo(s) sélectionnée(s)`);
        });
    }

    // Fonctions de suppression globales
    window.removeImagePreview = function(button, index) {
        button.closest('.group').remove();
        console.log(`🗑️ Aperçu image ${index} supprimé`);
    };

    window.removeVideoPreview = function(button, index) {
        button.closest('.group').remove();
        console.log(`🗑️ Aperçu vidéo ${index} supprimé`);
    };

    // Validation avant soumission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const images = imagesInput ? imagesInput.files : [];
            const videos = videosInput ? videosInput.files : [];
            
            console.log(`📋 Soumission: ${images.length} image(s), ${videos.length} vidéo(s)`);
            
            let hasError = false;
            
            // Validation images
            Array.from(images).forEach(file => {
                if (file.size > 2 * 1024 * 1024) {
                    alert(`❌ L'image "${file.name}" dépasse la taille maximale de 2MB`);
                    hasError = true;
                }
            });
            
            // Validation vidéos
            Array.from(videos).forEach(file => {
                if (file.size > 20 * 1024 * 1024) {
                    alert(`❌ La vidéo "${file.name}" dépasse la taille maximale de 20MB`);
                    hasError = true;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                return false;
            }

            // Afficher un indicateur de chargement
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2">⏳</span> Création en cours...';
            }
        });
    }

    console.log('✅ Formulaire initialisé avec succès');
});
</script>
@endpush

@endsection

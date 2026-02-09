@extends('admin.layouts.app')

@section('title', 'Modifier le produit - Jackson Energy International')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-montserrat font-bold text-gray-900">✏️ Modifier le produit</h1>
                <p class="text-gray-600 mt-1">Modifiez les informations de <span class="font-semibold text-green-600">"{{ $product->name }}"</span></p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                ← Retour à la liste
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="p-8 space-y-8" id="product-form">
            @csrf
            @method('PUT')

            {{-- Informations de base --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                    Informations de base
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nom du produit <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
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
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                        placeholder="Décrivez le produit en détail...">{{ old('description', $product->description) }}</textarea>
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
                        placeholder="Ex: Puissance: 300W, Voltage: 24V, Dimensions: 1640x992x40mm">{{ old('specifications', $product->specifications) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">📋 Détails techniques du produit</p>
                </div>
            </div>

            {{-- Prix et stock --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                    Prix et stock
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">
                            Prix normal (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="1" required
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
                            <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price', $product->promotional_price) }}" min="0" step="1"
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
                        <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
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
                    <input type="text" id="warranty" name="warranty" value="{{ old('warranty', $product->warranty) }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        placeholder="Ex: 2 ans, 12 mois, Garantie constructeur">
                    <p class="text-xs text-gray-500 mt-1">🛡️ Informations sur la garantie du produit</p>
                </div>
            </div>

            {{-- Gestion des médias --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                    Gestion des images et vidéos
                </h2>

                {{-- Images existantes --}}
                @if($product->hasImages())
                    <div class="mb-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center">
                                <span class="bg-blue-500 text-white px-2.5 py-0.5 rounded-full text-xs mr-2">{{ $product->images_count }}</span>
                                📸 Images actuelles
                            </h3>
                            <p class="text-xs text-gray-600">Cliquez sur ✕ pour supprimer une image</p>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                            @foreach($product->image_urls as $index => $imageUrl)
                                <div class="relative group transition-all duration-300" data-image-index="{{ $index }}">
                                    <img src="{{ $imageUrl }}" alt="Image {{ $index + 1 }}" 
                                         class="w-full h-24 object-cover rounded-lg shadow-md group-hover:shadow-xl transition border-2 border-transparent group-hover:border-blue-500">
                                    <button type="button"
                                            onclick="removeExistingImage({{ $index }}, this)"
                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-200 transform hover:scale-110"
                                            title="Supprimer cette image">
                                        ✕
                                    </button>
                                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white text-xs px-2 py-0.5 rounded">
                                        #{{ $index + 1 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Vidéos existantes --}}
                @if($product->hasVideos())
                    <div class="mb-8 p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center">
                                <span class="bg-purple-500 text-white px-2.5 py-0.5 rounded-full text-xs mr-2">{{ $product->videos_count }}</span>
                                🎥 Vidéos actuelles
                            </h3>
                            <p class="text-xs text-gray-600">Cliquez sur ✕ pour supprimer une vidéo</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($product->video_urls as $index => $videoUrl)
                                <div class="relative group bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-all duration-300" data-video-index="{{ $index }}">
                                    <video class="w-full h-32 object-cover" preload="metadata" controls>
                                        <source src="{{ $videoUrl }}" type="video/mp4">
                                        Votre navigateur ne supporte pas la lecture de vidéos.
                                    </video>
                                    <button type="button"
                                            onclick="removeExistingVideo({{ $index }}, this)"
                                            class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-200 transform hover:scale-110"
                                            title="Supprimer cette vidéo">
                                        ✕
                                    </button>
                                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-70 text-white text-xs px-2 py-0.5 rounded">
                                        Vidéo #{{ $index + 1 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Ajouter nouvelles images --}}
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        ➕ Ajouter de nouvelles images
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition-colors bg-gray-50 hover:bg-green-50">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="new_images" class="relative cursor-pointer bg-white rounded-md font-semibold text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 px-3 py-1">
                                    <span>Choisir des images</span>
                                    <input id="new_images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">JPG, PNG, GIF jusqu'à 2MB par image</p>
                        </div>
                    </div>
                    <div id="new-images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>

                {{-- Ajouter nouvelles vidéos --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        ➕ Ajouter de nouvelles vidéos
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition-colors bg-gray-50 hover:bg-green-50">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="new_videos" class="relative cursor-pointer bg-white rounded-md font-semibold text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 px-3 py-1">
                                    <span>Choisir des vidéos</span>
                                    <input id="new_videos" name="videos[]" type="file" class="sr-only" multiple accept="video/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">MP4, MOV, AVI jusqu'à 20MB par vidéo</p>
                        </div>
                    </div>
                    <div id="new-videos-preview" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                </div>

                {{-- ✅ CORRECTION: Champs cachés avec des tableaux dynamiques --}}
                <div id="remove-images-container"></div>
                <div id="remove-videos-container"></div>
            </div>

            {{-- Options --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">4</span>
                    Options du produit
                </h2>
                
                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            ✅ Produit actif (visible sur le site)
                        </span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
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
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">5</span>
                    Optimisation SEO
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">
                            🏷️ Titre SEO
                        </label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}" maxlength="60"
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
                            placeholder="Description optimisée pour les moteurs de recherche">{{ old('meta_description', $product->meta_description) }}</textarea>
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
                    💾 Mettre à jour le produit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de l\'édition du produit...');

    // ✅ CORRECTION: Utiliser des tableaux au lieu de strings
    let imagesToRemove = [];
    let videosToRemove = [];

    // Supprimer une image existante
    window.removeExistingImage = function(imageIndex, buttonElement) {
        if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette image ?')) {
            const imageContainer = buttonElement.closest('[data-image-index]');
            imageContainer.style.opacity = '0.3';
            imageContainer.style.filter = 'grayscale(100%)';
            imageContainer.style.pointerEvents = 'none';
            
            // ✅ Ajouter à la liste
            if (!imagesToRemove.includes(imageIndex)) {
                imagesToRemove.push(imageIndex);
            }
            
            // ✅ CORRECTION: Créer des champs cachés individuels
            updateRemoveImagesFields();
            
            console.log('🗑️ Image marquée pour suppression:', imageIndex);
            console.log('📋 Liste complète des images à supprimer:', imagesToRemove);
            
            if (window.showAdminNotification) {
                window.showAdminNotification('Image marquée pour suppression', 'warning');
            }
        }
    };

    // Supprimer une vidéo existante
    window.removeExistingVideo = function(videoIndex, buttonElement) {
        if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette vidéo ?')) {
            const videoContainer = buttonElement.closest('[data-video-index]');
            videoContainer.style.opacity = '0.3';
            videoContainer.style.filter = 'grayscale(100%)';
            videoContainer.style.pointerEvents = 'none';
            
            // ✅ Ajouter à la liste
            if (!videosToRemove.includes(videoIndex)) {
                videosToRemove.push(videoIndex);
            }
            
            // ✅ CORRECTION: Créer des champs cachés individuels
            updateRemoveVideosFields();
            
            console.log('🗑️ Vidéo marquée pour suppression:', videoIndex);
            console.log('📋 Liste complète des vidéos à supprimer:', videosToRemove);
            
            if (window.showAdminNotification) {
                window.showAdminNotification('Vidéo marquée pour suppression', 'warning');
            }
        }
    };

    // ✅ NOUVELLE FONCTION: Créer des champs cachés pour chaque image à supprimer
    function updateRemoveImagesFields() {
        const container = document.getElementById('remove-images-container');
        container.innerHTML = '';
        
        imagesToRemove.forEach(function(index) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_images[]';  // ✅ Nom de champ array
            input.value = index;
            container.appendChild(input);
        });
        
        console.log('✅ Champs de suppression d\'images mis à jour:', imagesToRemove);
    }

    // ✅ NOUVELLE FONCTION: Créer des champs cachés pour chaque vidéo à supprimer
    function updateRemoveVideosFields() {
        const container = document.getElementById('remove-videos-container');
        container.innerHTML = '';
        
        videosToRemove.forEach(function(index) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_videos[]';  // ✅ Nom de champ array
            input.value = index;
            container.appendChild(input);
        });
        
        console.log('✅ Champs de suppression de vidéos mis à jour:', videosToRemove);
    }

    // Prévisualisation nouvelles images
    const newImagesInput = document.getElementById('new_images');
    const newImagesPreview = document.getElementById('new-images-preview');
    
    if (newImagesInput && newImagesPreview) {
        newImagesInput.addEventListener('change', function(e) {
            newImagesPreview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative bg-white rounded-lg shadow-md overflow-hidden border-2 border-green-300';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover">
                            <div class="p-2 bg-green-50">
                                <p class="text-xs text-gray-700 font-medium truncate">${file.name}</p>
                                <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(1)} KB</p>
                            </div>
                            <div class="absolute top-1 right-1 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full shadow font-bold">NOUVEAU</div>
                        `;
                        newImagesPreview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            console.log(`📸 ${e.target.files.length} nouvelle(s) image(s) ajoutée(s)`);
        });
    }

    // Prévisualisation nouvelles vidéos
    const newVideosInput = document.getElementById('new_videos');
    const newVideosPreview = document.getElementById('new-videos-preview');
    
    if (newVideosInput && newVideosPreview) {
        newVideosInput.addEventListener('change', function(e) {
            newVideosPreview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                if (file.type.startsWith('video/')) {
                    const div = document.createElement('div');
                    div.className = 'relative bg-white rounded-lg shadow-md p-4 border-2 border-green-300';
                    div.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">${file.name}</p>
                                <p class="text-xs text-gray-600">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            </div>
                            <div class="bg-green-500 text-white text-xs px-2 py-1 rounded-full font-bold shadow">NOUVEAU</div>
                        </div>
                    `;
                    newVideosPreview.appendChild(div);
                }
            });
            
            console.log(`🎥 ${e.target.files.length} nouvelle(s) vidéo(s) ajoutée(s)`);
        });
    }

    // Validation avant soumission
    const form = document.getElementById('product-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('========================================');
            console.log('📋 SOUMISSION DU FORMULAIRE');
            console.log('========================================');
            console.log('🗑️ Images à supprimer:', imagesToRemove);
            console.log('🗑️ Vidéos à supprimer:', videosToRemove);
            console.log('📸 Nouvelles images:', newImagesInput.files.length);
            console.log('🎥 Nouvelles vidéos:', newVideosInput.files.length);
            console.log('========================================');
            
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2">⏳</span> Mise à jour en cours...';
            }
        });
    }

    console.log('✅ Formulaire d\'édition initialisé avec succès');
});
</script>
@endpush

@endsection
@extends('admin.layouts.app')

@section('title', 'Modifier le produit - Administration')

@section('content')
<div class="admin-form-bf">
    <div class="mb-8">
        <h1 class="text-3xl font-montserrat font-bold text-gray-900">Modifier le produit</h1>
        <p class="text-gray-600">Modifiez les informations du produit "{{ $product->name }}"</p>
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
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Nom du produit *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
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
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
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
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="specifications" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Spécifications techniques</label>
                <textarea id="specifications" name="specifications" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('specifications', $product->specifications) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="price" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Prix (FCFA) *</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="1" required
                        class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="promotional_price" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Prix promotionnel (FCFA)</label>
                    <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price', $product->promotional_price) }}" min="0" step="1"
                        class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                </div>
                
                <div>
                    <label for="stock_quantity" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Quantité en stock *</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
                        class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="warranty" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Garantie</label>
                <input type="text" id="warranty" name="warranty" value="{{ old('warranty', $product->warranty) }}" placeholder="Ex: 2 ans"
                    class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
            </div>

            <!-- ✅ SECTION MÉDIAS EXISTANTS ET NOUVEAUX -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📸 Gestion des images et vidéos</h3>
                
                <!-- ✅ IMAGES EXISTANTES -->
                @if($product->hasImages())
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs mr-2">{{ $product->images_count }}</span>
                            Images actuelles
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($product->image_urls as $index => $imageUrl)
                                <div class="relative group" data-image-index="{{ $index }}">
                                    <img src="{{ $imageUrl }}" alt="Image {{ $index + 1 }}" 
                                         class="w-full h-20 object-cover rounded-lg shadow-sm border">
                                    <button type="button"
                                            onclick="removeExistingImage({{ $index }}, this)"
                                            class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-all duration-200"
                                            title="Supprimer cette image">
                                        ✕
                                    </button>
                                    <div class="absolute bottom-1 left-1 bg-black bg-opacity-60 text-white text-xs px-1 rounded">
                                        {{ $index + 1 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ✅ VIDÉOS EXISTANTES -->
                @if($product->hasVideos())
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded-full text-xs mr-2">{{ $product->videos_count }}</span>
                            Vidéos actuelles
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($product->video_urls as $index => $videoUrl)
                                <div class="relative group bg-gray-100 rounded-lg overflow-hidden" data-video-index="{{ $index }}">
                                    <video class="w-full h-24 object-cover" preload="metadata" controls>
                                        <source src="{{ $videoUrl }}" type="video/mp4">
                                        Votre navigateur ne supporte pas la lecture de vidéos.
                                    </video>
                                    <button type="button"
                                            onclick="removeExistingVideo({{ $index }}, this)"
                                            class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition-all duration-200"
                                            title="Supprimer cette vidéo">
                                        ✕
                                    </button>
                                    <div class="absolute bottom-1 left-1 bg-black bg-opacity-60 text-white text-xs px-1 rounded">
                                        Vidéo {{ $index + 1 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- ✅ AJOUT DE NOUVELLES IMAGES -->
                <div class="mb-6">
                    <label for="new_images" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">
                        ➕ Ajouter de nouvelles images
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-vert-energie transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="new_images" class="relative cursor-pointer bg-white rounded-md font-medium text-vert-energie hover:text-green-500">
                                    <span>Sélectionner des images</span>
                                    <input id="new_images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">JPG, PNG, GIF jusqu'à 2MB chacune</p>
                        </div>
                    </div>
                    <div id="new-images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>

                <!-- ✅ AJOUT DE NOUVELLES VIDÉOS -->
                <div class="mb-4">
                    <label for="new_videos" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">
                        ➕ Ajouter de nouvelles vidéos
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-vert-energie transition">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="new_videos" class="relative cursor-pointer bg-white rounded-md font-medium text-vert-energie hover:text-green-500">
                                    <span>Sélectionner des vidéos</span>
                                    <input id="new_videos" name="videos[]" type="file" class="sr-only" multiple accept="video/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">MP4, MOV, AVI jusqu'à 20MB chacune</p>
                        </div>
                    </div>
                    <div id="new-videos-preview" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                </div>

                <!-- ✅ CHAMPS CACHÉS pour gérer les suppressions -->
                <input type="hidden" name="remove_images" id="remove_images" value="">
                <input type="hidden" name="remove_videos" id="remove_videos" value="">
            </div>

            <div class="flex flex-wrap gap-6">
                <label class="flex items-center admin-label-bf">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-vert-energie shadow-sm focus:border-vert-energie focus:ring focus:ring-vert-energie focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Produit actif</span>
                </label>
                <label class="flex items-center admin-label-bf">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                        class="rounded admin-input-bf border-gray-300 text-vert-energie shadow-sm focus:border-vert-energie focus:ring focus:ring-vert-energie focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-700">Produit vedette</span>
                </label>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔍 Optimisation SEO</h3>
                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Titre SEO</label>
                        <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"
                            class="w-full admin-input-bf px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                    </div>
                    <div>
                        <label for="meta_description" class="block admin-label-bf text-sm font-medium text-gray-700 mb-2">Description SEO</label>
                        <textarea id="meta_description" name="meta_description" rows="2"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-between pt-6 border-t">
                <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    ← Retour
                </a>
                <button type="submit" class="bg-vert-energie admin-btn-bf text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    💾 Mettre à jour le produit
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation de l\'édition produit...');

    // ✅ GESTION DES SUPPRESSIONS
    let imagesToRemove = [];
    let videosToRemove = [];

    window.removeExistingImage = function(imageIndex, buttonElement) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
            // Masquer visuellement
            const imageContainer = buttonElement.closest('[data-image-index]');
            imageContainer.style.opacity = '0.3';
            imageContainer.style.pointerEvents = 'none';
            
            // Ajouter à la liste des suppressions
            imagesToRemove.push(imageIndex);
            document.getElementById('remove_images').value = imagesToRemove.join(',');
            
            console.log('🗑️ Image marquée pour suppression:', imageIndex);
        }
    };

    window.removeExistingVideo = function(videoIndex, buttonElement) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette vidéo ?')) {
            // Masquer visuellement
            const videoContainer = buttonElement.closest('[data-video-index]');
            videoContainer.style.opacity = '0.3';
            videoContainer.style.pointerEvents = 'none';
            
            // Ajouter à la liste des suppressions
            videosToRemove.push(videoIndex);
            document.getElementById('remove_videos').value = videosToRemove.join(',');
            
            console.log('🗑️ Vidéo marquée pour suppression:', videoIndex);
        }
    };

    // ✅ PRÉVISUALISATION NOUVELLES IMAGES
    const newImagesInput = document.getElementById('new_images');
    const newImagesPreview = document.getElementById('new-images-preview');
    
    newImagesInput.addEventListener('change', function(e) {
        newImagesPreview.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative bg-white rounded-lg shadow border';
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-20 object-cover rounded-t-lg">
                        <div class="p-2">
                            <p class="text-xs text-gray-600 truncate">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(1)} MB</p>
                        </div>
                        <div class="absolute top-1 right-1 bg-green-500 text-white text-xs px-1 rounded">NOUVEAU</div>
                    `;
                    newImagesPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
        
        console.log(`📸 ${e.target.files.length} nouvelle(s) image(s) sélectionnée(s)`);
    });

    // ✅ PRÉVISUALISATION NOUVELLES VIDÉOS
    const newVideosInput = document.getElementById('new_videos');
    const newVideosPreview = document.getElementById('new-videos-preview');
    
    newVideosInput.addEventListener('change', function(e) {
        newVideosPreview.innerHTML = '';
        
        Array.from(e.target.files).forEach((file, index) => {
            if (file.type.startsWith('video/')) {
                const div = document.createElement('div');
                div.className = 'relative bg-white rounded-lg shadow border p-4';
                div.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">${file.name}</p>
                            <p class="text-xs text-gray-600">${(file.size / 1024 / 1024).toFixed(1)} MB</p>
                        </div>
                        <div class="bg-green-500 text-white text-xs px-2 py-1 rounded">NOUVEAU</div>
                    </div>
                `;
                newVideosPreview.appendChild(div);
            }
        });
        
        console.log(`🎥 ${e.target.files.length} nouvelle(s) vidéo(s) sélectionnée(s)`);
    });

    // ✅ VALIDATION AVANT SOUMISSION
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const newImages = newImagesInput.files;
        const newVideos = newVideosInput.files;
        
        console.log(`📋 Soumission: ${imagesToRemove.length} image(s) supprimée(s), ${videosToRemove.length} vidéo(s) supprimée(s)`);
        console.log(`📋 Ajout: ${newImages.length} nouvelle(s) image(s), ${newVideos.length} nouvelle(s) vidéo(s)`);
    });
});
</script>
@endpush

@endsection

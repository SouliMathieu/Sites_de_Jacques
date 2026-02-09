@extends('admin.layouts.app')

@section('title', 'Ajouter une catégorie - Jackson Energy International')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-montserrat font-bold text-gray-900">📁 Ajouter une catégorie</h1>
                <p class="text-gray-600 mt-1">Créez une nouvelle catégorie pour organiser vos produits</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                ← Retour à la liste
            </a>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="bg-white rounded-lg shadow-lg">
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            
            {{-- Informations de base --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">1</span>
                    Informations de base
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nom de la catégorie --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nom de la catégorie <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="Ex: Panneaux solaires"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">📝 Nom court et descriptif</p>
                    </div>
                    
                    {{-- Ordre d'affichage --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-2">
                            Ordre d'affichage <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', 0) }}" 
                               required
                               min="0"
                               placeholder="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('sort_order') border-red-500 @enderror">
                        @error('sort_order')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">🔢 Plus petit = affiché en premier</p>
                    </div>
                </div>

                {{-- Slug (auto-généré) --}}
                <div class="mt-6">
                    <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                        Slug (URL)
                    </label>
                    <input type="text" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug') }}" 
                           placeholder="panneaux-solaires"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition bg-gray-50">
                    <p class="text-xs text-gray-500 mt-1">🔗 Généré automatiquement si vide</p>
                </div>

                {{-- Description --}}
                <div class="mt-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4" 
                              placeholder="Décrivez cette catégorie de produits..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('description') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">📄 Description visible sur le site (optionnel)</p>
                </div>
            </div>
            
            {{-- Image et média --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                    Image de la catégorie (optionnel)
                </h2>

                {{-- Option 1: Upload fichier --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        📸 Uploader une image
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition-colors bg-gray-50 hover:bg-green-50">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="image_file" class="relative cursor-pointer bg-white rounded-md font-semibold text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 px-3 py-1">
                                    <span>Choisir une image</span>
                                    <input id="image_file" name="image_file" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 2MB</p>
                        </div>
                    </div>
                    <div id="image-preview" class="mt-4 hidden">
                        <div class="relative inline-block">
                            <img id="preview-img" src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg border-2 border-green-500 shadow-md">
                            <button type="button" onclick="removeImagePreview()" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm shadow-lg">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Option 2: URL --}}
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">OU</span>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="image_url" class="block text-sm font-semibold text-gray-700 mb-2">
                        🔗 URL de l'image
                    </label>
                    <input type="url" 
                           id="image_url" 
                           name="image_url" 
                           value="{{ old('image_url') }}" 
                           placeholder="https://exemple.com/image.jpg"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-500 mt-1">🌐 Si vous avez déjà une image en ligne</p>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700">
                        💡 <strong>Note:</strong> Si aucune image n'est fournie, une image par défaut avec les initiales de la catégorie sera générée automatiquement.
                    </p>
                </div>
            </div>
            
            {{-- Options --}}
            <div class="pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                    Options de visibilité
                </h2>

                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            ✅ Catégorie active (visible sur le site)
                        </span>
                    </label>

                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               name="is_featured" 
                               value="1" 
                               {{ old('is_featured') ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            ⭐ Catégorie mise en avant (page d'accueil)
                        </span>
                    </label>
                </div>
            </div>
            
            {{-- Boutons d'action --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6 border-t">
                <a href="{{ route('admin.categories.index') }}" 
                   class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                    ← Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                    💾 Créer la catégorie
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Formulaire de création de catégorie initialisé');

    // Preview de l'image uploadée
    const imageInput = document.getElementById('image_file');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                console.log('📸 Image sélectionnée:', file.name);
            }
        });
    }

    // Auto-génération du slug depuis le nom
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (!slugInput.value || slugInput.dataset.autoGenerated) {
                const slug = this.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });

        slugInput.addEventListener('input', function() {
            if (this.value) {
                delete this.dataset.autoGenerated;
            }
        });
    }
});

// Fonction pour supprimer la preview d'image
function removeImagePreview() {
    document.getElementById('image_file').value = '';
    document.getElementById('image-preview').classList.add('hidden');
    console.log('🗑️ Preview d\'image supprimée');
}

console.log('✅ Script de création de catégorie chargé');
</script>
@endpush

@endsection
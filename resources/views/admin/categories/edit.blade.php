@extends('admin.layouts.app')

@section('title', 'Modifier la catégorie - Jackson Energy International')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-montserrat font-bold text-gray-900">✏️ Modifier la catégorie</h1>
                <p class="text-gray-600 mt-1">
                    Modifiez les informations de <span class="font-semibold text-green-600">"{{ $category->name }}"</span>
                </p>
            </div>
            <a href="{{ route('admin.categories.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                ← Retour à la liste
            </a>
        </div>
    </div>

    {{-- Messages de feedback --}}
    @if(session('success'))
    <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md animate-fade-in">
        <div class="flex items-center">
            <span class="text-2xl mr-3">✅</span>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-md">
        <div class="flex items-start">
            <span class="text-2xl mr-3">⚠️</span>
            <div class="flex-1">
                <p class="font-semibold mb-2">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Formulaire --}}
    <div class="bg-white rounded-lg shadow-lg">
        <form action="{{ route('admin.categories.update', $category->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="p-8 space-y-8">
            @csrf
            @method('PUT')

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
                               value="{{ old('name', $category->name) }}" 
                               required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1 flex items-center">
                                <span class="mr-1">⚠️</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                            Slug (URL)
                        </label>
                        <input type="text" 
                               id="slug" 
                               name="slug" 
                               value="{{ old('slug', $category->slug) }}" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition bg-gray-50">
                        <p class="text-xs text-gray-500 mt-1">🔗 Modifiable si nécessaire</p>
                    </div>
                </div>

                {{-- Description --}}
                <div class="mt-6">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('description', $category->description) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">📄 Description visible sur le site</p>
                </div>
            </div>

            {{-- Image actuelle et nouvelle --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">2</span>
                    Image de la catégorie
                </h2>

                {{-- Image actuelle --}}
                @if($category->image)
                <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">📸 Image actuelle</label>
                    <div class="relative inline-block">
                        <img src="{{ $category->image }}" 
                             alt="{{ $category->name }}" 
                             class="h-32 w-32 object-cover rounded-lg border-2 border-blue-500 shadow-md">
                        <div class="absolute -top-2 -right-2 bg-blue-500 text-white rounded-full px-2 py-1 text-xs font-bold">
                            Actuelle
                        </div>
                    </div>
                </div>
                @endif

                {{-- Nouvelle image par upload --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        ➕ Uploader une nouvelle image
                    </label>
                    <div class="mt-2 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-500 transition-colors bg-gray-50 hover:bg-green-50">
                        <div class="space-y-2 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="image_file" class="relative cursor-pointer bg-white rounded-md font-semibold text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-green-500 px-3 py-1">
                                    <span>Choisir une nouvelle image</span>
                                    <input id="image_file" name="image_file" type="file" class="sr-only" accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF jusqu'à 2MB</p>
                        </div>
                    </div>
                    <div id="new-image-preview" class="mt-4 hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">👀 Preview nouvelle image</label>
                        <div class="relative inline-block">
                            <img id="new-preview-img" src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg border-2 border-green-500 shadow-md">
                            <button type="button" onclick="removeNewImagePreview()" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm shadow-lg">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Séparateur --}}
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500 font-medium">OU</span>
                    </div>
                </div>

                {{-- URL de l'image --}}
                <div class="mt-6">
                    <label for="image_url" class="block text-sm font-semibold text-gray-700 mb-2">
                        🔗 URL de l'image
                    </label>
                    <input type="url" 
                           id="image_url" 
                           name="image_url" 
                           value="{{ old('image_url', $category->image) }}" 
                           placeholder="https://exemple.com/image.jpg"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-500 mt-1">🌐 Remplace l'image actuelle si renseignée</p>
                </div>
            </div>
            
            {{-- Paramètres avancés --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">3</span>
                    Paramètres avancés
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Ordre d'affichage --}}
                    <div>
                        <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-2">
                            Ordre d'affichage
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               value="{{ old('sort_order', $category->sort_order ?? 0) }}" 
                               min="0" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <p class="text-xs text-gray-500 mt-1">🔢 Plus petit = affiché en premier</p>
                    </div>

                    {{-- Icône --}}
                    <div>
                        <label for="icon" class="block text-sm font-semibold text-gray-700 mb-2">
                            Icône (emoji ou code)
                        </label>
                        <input type="text" 
                               id="icon" 
                               name="icon" 
                               value="{{ old('icon', $category->icon ?? '') }}" 
                               placeholder="☀️ ou <i class='fas fa-solar-panel'>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <p class="text-xs text-gray-500 mt-1">✨ Emoji ou code HTML FontAwesome</p>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="border-b pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">4</span>
                    Optimisation SEO
                </h2>

                <div class="space-y-4">
                    <div>
                        <label for="meta_title" class="block text-sm font-semibold text-gray-700 mb-2">
                            🏷️ Titre SEO
                        </label>
                        <input type="text" 
                               id="meta_title" 
                               name="meta_title" 
                               value="{{ old('meta_title', $category->meta_title ?? '') }}" 
                               maxlength="60"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                        <p class="text-xs text-gray-500 mt-1">Recommandé: 50-60 caractères</p>
                    </div>

                    <div>
                        <label for="meta_description" class="block text-sm font-semibold text-gray-700 mb-2">
                            📝 Description SEO
                        </label>
                        <textarea id="meta_description" 
                                  name="meta_description" 
                                  rows="3" 
                                  maxlength="160"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition">{{ old('meta_description', $category->meta_description ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Recommandé: 150-160 caractères</p>
                    </div>
                </div>
            </div>
            
            {{-- Options --}}
            <div class="pb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-blue-100 text-blue-600 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm">5</span>
                    Options de visibilité
                </h2>

                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $category->is_active) ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            ✅ Catégorie active (visible sur le site)
                        </span>
                    </label>

                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               name="show_in_menu" 
                               value="1" 
                               {{ old('show_in_menu', $category->show_in_menu ?? true) ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            🔗 Afficher dans le menu de navigation
                        </span>
                    </label>

                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               name="is_featured" 
                               value="1" 
                               {{ old('is_featured', $category->is_featured ?? false) ? 'checked' : '' }} 
                               class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring focus:ring-green-500 focus:ring-opacity-50 w-5 h-5">
                        <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-green-600 transition">
                            ⭐ Catégorie mise en avant (page d'accueil)
                        </span>
                    </label>
                </div>
            </div>

            {{-- Informations système --}}
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-sm font-bold text-gray-700 mb-3">ℹ️ Informations système</h3>
                <div class="grid grid-cols-2 gap-4 text-xs text-gray-600">
                    <div>
                        <span class="font-medium">ID:</span> #{{ $category->id }}
                    </div>
                    <div>
                        <span class="font-medium">Produits:</span> {{ $category->products_count ?? 0 }}
                    </div>
                    <div>
                        <span class="font-medium">Créée:</span> {{ $category->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <span class="font-medium">Modifiée:</span> {{ $category->updated_at->format('d/m/Y H:i') }}
                    </div>
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
                    💾 Mettre à jour la catégorie
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Formulaire d\'édition de catégorie initialisé');

    // Preview de la nouvelle image uploadée
    const newImageInput = document.getElementById('image_file');
    const newImagePreview = document.getElementById('new-image-preview');
    const newPreviewImg = document.getElementById('new-preview-img');

    if (newImageInput) {
        newImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    newPreviewImg.src = e.target.result;
                    newImagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
                console.log('📸 Nouvelle image sélectionnée:', file.name);
            }
        });
    }

    // Auto-génération du slug depuis le nom (si modifié)
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            if (slugInput.dataset.allowAutoUpdate) {
                const slug = this.value
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });

        slugInput.addEventListener('focus', function() {
            this.dataset.allowAutoUpdate = 'true';
        });
    }

    // Confirmation avant soumission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2">⏳</span> Mise à jour en cours...';
            }
        });
    }

    console.log('✅ Catégorie "{{ $category->name }}" prête pour modification');
});

// Fonction pour supprimer la preview de nouvelle image
function removeNewImagePreview() {
    document.getElementById('image_file').value = '';
    document.getElementById('new-image-preview').classList.add('hidden');
    console.log('🗑️ Preview de nouvelle image supprimée');
}

console.log('✅ Script d\'édition de catégorie chargé');
</script>
@endpush

@endsection

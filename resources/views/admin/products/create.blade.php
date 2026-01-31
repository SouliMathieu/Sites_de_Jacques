@extends('admin.layouts.app')

@section('title', 'Ajouter un produit - Administration')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-montserrat font-bold text-gray-900">Ajouter un produit</h1>
    <p class="text-gray-600">Créez un nouveau produit pour votre catalogue</p>
</div>

<div class="bg-white rounded-lg shadow">
    <form method="POST" action="{{ route('admin.products.store') }}" class="p-6 space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom du produit *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Catégorie *</label>
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
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
            <textarea id="description" name="description" rows="4" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="specifications" class="block text-sm font-medium text-gray-700 mb-2">Spécifications techniques</label>
            <textarea id="specifications" name="specifications" rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('specifications') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Prix (FCFA) *</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" min="0" step="1" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('price') border-red-500 @enderror">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="promotional_price" class="block text-sm font-medium text-gray-700 mb-2">Prix promotionnel (FCFA)</label>
                <input type="number" id="promotional_price" name="promotional_price" value="{{ old('promotional_price') }}" min="0" step="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
            </div>
            <div>
                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantité en stock *</label>
                <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('stock_quantity') border-red-500 @enderror">
                @error('stock_quantity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="warranty" class="block text-sm font-medium text-gray-700 mb-2">Garantie</label>
            <input type="text" id="warranty" name="warranty" value="{{ old('warranty') }}" placeholder="Ex: 2 ans"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
        </div>

        <!-- Section Dropzone pour images et vidéos -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Images et vidéos du produit</label>

            <!-- IMPORTANT: Div avec l'ID my-dropzone -->
            <div id="my-dropzone" class="mt-4">
                <!-- Le contenu sera généré par SimpleDropzone -->
            </div>

            <input type="hidden" name="images" id="images" value="{{ old('images') }}">
            <input type="hidden" name="videos" id="videos" value="{{ old('videos') }}">

            <small class="block text-xs text-gray-400 mt-2">
                Formats acceptés : JPG, PNG, GIF, WEBP, MP4, MOV, AVI – Taille max : 20 Mo par fichier
            </small>

            @error('images')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-wrap gap-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-vert-energie shadow-sm focus:border-vert-energie focus:ring focus:ring-vert-energie focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Produit actif</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-vert-energie shadow-sm focus:border-vert-energie focus:ring focus:ring-vert-energie focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Produit vedette</span>
            </label>
        </div>

        <div class="border-t pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Optimisation SEO</h3>
            <div class="space-y-4">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">Titre SEO</label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                </div>
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">Description SEO</label>
                    <textarea id="meta_description" name="meta_description" rows="2"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('meta_description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-between pt-6 border-t">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                ← Retour
            </a>
            <button type="submit" class="bg-vert-energie text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                💾 Créer le produit
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation SimpleDropzone...');

    // Vérifier que l'élément existe
    const dropzoneElement = document.getElementById('my-dropzone');
    if (!dropzoneElement) {
        console.error('❌ Élément #my-dropzone non trouvé dans le DOM');
        return;
    }

    console.log('✅ Élément #my-dropzone trouvé');

    let imagePaths = [];
    let videoPaths = [];

    try {
        const myDropzone = new Dropzone("#my-dropzone", {
            url: "{{ route('admin.upload-files') }}",
            maxFilesize: 20,
            acceptedFiles: ".jpg,.jpeg,.png,.gif,.webp,.mp4,.mov,.avi",
            dictDefaultMessage: "📁 Glissez-déposez vos fichiers ici ou cliquez pour sélectionner",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            init: function() {
                console.log('🎉 SimpleDropzone initialisée avec succès !');
            },
            success: function(file, response) {
                console.log('📤 Upload réussi:', response);
                if (response && response.success && response.files) {
                    response.files.forEach(fileData => {
                        if (fileData.type === 'image') {
                            imagePaths.push(fileData.url);
                        } else if (fileData.type === 'video') {
                            videoPaths.push(fileData.url);
                        }
                    });
                    updateInputs();
                }
            },
            error: function(file, errorMessage) {
                console.error('❌ Erreur upload:', errorMessage);
            }
        });

        function updateInputs() {
            document.getElementById('images').value = imagePaths.join(',');
            document.getElementById('videos').value = videoPaths.join(',');
            console.log('📝 Inputs mis à jour - Images:', imagePaths.length, 'Vidéos:', videoPaths.length);
        }

    } catch (error) {
        console.error('❌ Erreur lors de l\'initialisation SimpleDropzone:', error);
    }
});
</script>
@endpush

@endsection

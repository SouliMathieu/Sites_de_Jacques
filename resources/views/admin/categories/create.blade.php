@extends('admin.layouts.app')

@section('title', 'Ajouter une catégorie - Administration')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-montserrat font-bold text-gray-900">Ajouter une catégorie</h1>
    <p class="text-gray-600">Créez une nouvelle catégorie pour organiser vos produits</p>
</div>

<div class="bg-white rounded-lg shadow">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="p-6 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nom de la catégorie -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom de la catégorie *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie @error('name') border-red-500 @enderror">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Ordre d'affichage -->
            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Ordre d'affichage</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
                <p class="text-sm text-gray-500 mt-1">Plus le nombre est petit, plus la catégorie apparaîtra en premier</p>
            </div>
        </div>
        
        <!-- Description -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">{{ old('description') }}</textarea>
        </div>
        
        <!-- Image -->
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">URL de l'image</label>
            <input type="url" id="image" name="image" value="{{ old('image') }}" placeholder="https://exemple.com/image.jpg" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie">
            <p class="text-sm text-gray-500 mt-1">URL complète vers l'image de la catégorie</p>
        </div>
        
        <!-- Options -->
        <div>
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-vert-energie shadow-sm focus:border-vert-energie focus:ring focus:ring-vert-energie focus:ring-opacity-50">
                <span class="ml-2 text-sm text-gray-700">Catégorie active</span>
            </label>
        </div>
        
        <!-- Boutons -->
        <div class="flex justify-between pt-6 border-t">
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                ← Retour
            </a>
            <button type="submit" class="bg-vert-energie text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                💾 Créer la catégorie
            </button>
        </div>
    </form>
</div>
@endsection

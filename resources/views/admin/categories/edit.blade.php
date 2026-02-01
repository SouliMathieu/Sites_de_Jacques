@extends('admin.layouts.app')

@section('title', 'Modifier la catégorie - Administration')

@section('content')
<div class="admin-categories-burkina">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-montserrat font-bold text-gray-900">Modifier la catégorie</h1>
            <p class="text-gray-600">Modifiez les informations de "{{ $category->name }}"</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
            ← Retour
        </a>
    </div>

    <div class="admin-form-bf">
        @if(session('success'))
        <div class="admin-message-success">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="admin-message-error">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="admin-label-bf">Nom de la catégorie *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="admin-input-bf" required>
            </div>

            <div>
                <label for="description" class="admin-label-bf">Description</label>
                <textarea id="description" name="description" rows="4" class="admin-input-bf">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label for="sort_order" class="admin-label-bf">Ordre d'affichage</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" class="admin-input-bf">
                <p class="text-sm text-gray-600 mt-1">Plus le nombre est petit, plus la catégorie apparaîtra en premier</p>
            </div>

            <div>
                <label for="image" class="admin-label-bf">Image</label>
                <input type="url" id="image" name="image" value="{{ old('image', $category->image) }}" class="admin-input-bf" placeholder="https://exemple.com/image.jpg">
                <p class="text-sm text-gray-600 mt-1">URL complète vers l'image de la catégorie</p>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} class="mr-2">
                <label for="is_active" class="admin-label-bf mb-0">Catégorie active</label>
            </div>

            <div class="flex space-x-4 mt-6">
                <button type="submit" class="admin-btn-bf">
                    ✏️ Mettre à jour
                </button>
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

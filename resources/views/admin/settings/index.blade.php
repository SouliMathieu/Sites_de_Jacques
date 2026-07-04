@extends('admin.layouts.app')

@section('title', 'Paramètres du site')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">⚙️ Paramètres du site</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABS --}}
    <div>
        <div class="flex flex-wrap gap-2 mb-6 border-b border-gray-200">
            <button onclick="showTab('contact')" id="btn-contact" class="tab-btn px-4 py-2 text-sm transition border-b-2">📞 Contact</button>
            <button onclick="showTab('social')" id="btn-social" class="tab-btn px-4 py-2 text-sm transition border-b-2">🌐 Réseaux sociaux</button>
            <button onclick="showTab('slider')" id="btn-slider" class="tab-btn px-4 py-2 text-sm transition border-b-2">🖼️ Hero Slider</button>
            <button onclick="showTab('about')" id="btn-about" class="tab-btn px-4 py-2 text-sm transition border-b-2">🏢 À propos</button>
            <button onclick="showTab('account')" id="btn-account" class="tab-btn px-4 py-2 text-sm transition border-b-2">🔐 Compte admin</button>
        </div>

        {{-- CONTACT --}}
        <div id="tab-contact" class="tab-content">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-xl shadow p-6 space-y-4 max-w-2xl">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input type="text" name="phone" value="{{ $settings['phone']->value ?? '' }}" placeholder="+226 77 12 65 19"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro WhatsApp (sans +)</label>
                    <input type="text" name="whatsapp" value="{{ $settings['whatsapp']->value ?? '' }}" placeholder="22663952032"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email de contact</label>
                    <input type="text" name="email" value="{{ $settings['email']->value ?? '' }}" placeholder="info@jacksonenergy.bf"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                    <input type="text" name="address" value="{{ $settings['address']->value ?? '' }}" placeholder="Ouagadougou, Burkina Faso"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horaires d'ouverture</label>
                    <input type="text" name="opening_hours" value="{{ $settings['opening_hours']->value ?? '' }}" placeholder="Lun - Sam : 08h00 - 18h00"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Google Maps (URL embed)</label>
                    <p class="text-xs text-gray-500 mb-1">Google Maps → Partager → Intégrer une carte → copier le lien src="..."</p>
                    <textarea name="maps_embed" rows="3" placeholder="https://www.google.com/maps/embed?pb=..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">{{ $settings['maps_embed']->value ?? '' }}</textarea>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-sm">
                    Enregistrer
                </button>
            </form>
        </div>

        {{-- RÉSEAUX SOCIAUX --}}
        <div id="tab-social" class="tab-content hidden">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-xl shadow p-6 space-y-4 max-w-2xl">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">📘 Facebook URL</label>
                    <input type="url" name="facebook_url" value="{{ $settings['facebook_url']->value ?? '' }}" placeholder="https://facebook.com/..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">📸 Instagram URL</label>
                    <input type="url" name="instagram_url" value="{{ $settings['instagram_url']->value ?? '' }}" placeholder="https://instagram.com/..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">🐦 Twitter/X URL</label>
                    <input type="url" name="twitter_url" value="{{ $settings['twitter_url']->value ?? '' }}" placeholder="https://twitter.com/..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-sm">
                    Enregistrer
                </button>
            </form>
        </div>

        {{-- HERO SLIDER --}}
        <div id="tab-slider" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow p-6 max-w-2xl">
                <p class="text-sm text-gray-600 mb-4">Ajoutez ou supprimez les images du slider de la page d'accueil.</p>
                @php
                    $sliderImages = json_decode(\App\Models\SiteSetting::get('slider_images', '[]'), true) ?? [];
                @endphp
                @if(count($sliderImages) > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
                        @foreach($sliderImages as $img)
                            <div class="relative group">
                                <img src="{{ Storage::url($img) }}" class="w-full h-32 object-cover rounded-lg">
                                <form action="{{ route('admin.settings.slider.delete') }}" method="POST"
                                    class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $img }}">
                                    <button type="submit" class="bg-red-500 text-white rounded-full w-6 h-6 text-xs hover:bg-red-700">✕</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 mb-4">Aucune image dans le slider.</p>
                @endif
                <form action="{{ route('admin.settings.slider.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter une image</label>
                    <input type="file" name="slider_image" accept="image/*" required
                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100 mb-3">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-sm">
                        Ajouter au slider
                    </button>
                </form>
            </div>
        </div>

        {{-- À PROPOS --}}
        <div id="tab-about" class="tab-content hidden">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="bg-white rounded-xl shadow p-6 space-y-4 max-w-2xl">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slogan</label>
                    <input type="text" name="slogan" value="{{ $settings['slogan']->value ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titre À propos</label>
                    <input type="text" name="about_title" value="{{ $settings['about_title']->value ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texte À propos</label>
                    <textarea name="about_text" rows="5"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">{{ $settings['about_text']->value ?? '' }}</textarea>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-sm">
                    Enregistrer
                </button>
            </form>
        </div>

        {{-- COMPTE ADMIN --}}
        <div id="tab-account" class="tab-content hidden">
            <form action="{{ route('admin.settings.account') }}" method="POST" class="bg-white rounded-xl shadow p-6 space-y-4 max-w-2xl">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ auth()->user()->email }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <hr>
                <p class="text-sm text-gray-500">Laissez vide pour ne pas changer le mot de passe</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                    <input type="password" name="current_password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                    <input type="password" name="new_password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="new_password_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-semibold text-sm">
                    Mettre à jour le compte
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-green-600', 'text-green-600', 'font-semibold');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById('tab-' + name).classList.remove('hidden');
    const btn = document.getElementById('btn-' + name);
    btn.classList.add('border-green-600', 'text-green-600', 'font-semibold');
    btn.classList.remove('border-transparent', 'text-gray-500');
}
showTab('contact');
</script>
@endsection
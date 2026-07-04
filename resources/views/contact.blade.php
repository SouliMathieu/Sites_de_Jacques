@extends('layouts.app', ['title' => 'Contact - Jackson Energy International', 'description' => 'Contactez Jackson Energy pour vos besoins en énergie solaire. Devis gratuit, installation, maintenance au Burkina Faso.'])

@section('content')
@php
    $phone    = \App\Models\SiteSetting::get('phone', '+226 77 12 65 19');
    $whatsapp = \App\Models\SiteSetting::get('whatsapp', '22663952032');
    $email    = \App\Models\SiteSetting::get('email', 'info@jacksonenergy.bf');
    $hours    = \App\Models\SiteSetting::get('opening_hours', 'Lun - Sam : 08h00 - 18h00');
    $maps     = \App\Models\SiteSetting::get('maps_embed', '');
    $address  = \App\Models\SiteSetting::get('address', 'Ouagadougou, Burkina Faso');
    $telUrl   = 'tel:' . preg_replace('/\s+/', '', $phone);
    $waUrl    = 'https://wa.me/' . $whatsapp;
@endphp

    {{-- Header de la page --}}
    <section class="bg-gradient-to-r from-blue-50 to-green-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-600 mb-4">
                <a href="{{ route('home') }}" class="hover:text-green-600 transition">Accueil</a>
                <span class="mx-2">/</span>
                <span class="text-green-600 font-semibold">Contact</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Contactez-nous</h1>
            <p class="text-gray-600 text-base sm:text-lg">
                Notre équipe d'experts est à votre disposition pour vous conseiller
            </p>
        </div>
    </section>

    {{-- Section Contact --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                
                {{-- Coordonnées --}}
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Nos coordonnées</h2>

                    {{-- Téléphone --}}
                    <div class="flex items-start gap-4 mb-6 p-4 bg-orange-50 rounded-lg hover:shadow-md transition">
                        <div class="bg-orange-500 text-white rounded-full p-3 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 mb-1">Téléphone</h3>
                            <p class="text-gray-600 text-sm mb-2">Appelez-nous directement</p>
                            <a href="{{ $telUrl }}" class="text-orange-600 hover:text-orange-700 font-bold text-lg transition">
                                {{ $phone }}
                            </a>
                        </div>
                    </div>

                    {{-- WhatsApp --}}
                    <div class="flex items-start gap-4 mb-6 p-4 bg-green-50 rounded-lg hover:shadow-md transition">
                        <div class="bg-green-500 text-white rounded-full p-3 flex-shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 mb-1">WhatsApp</h3>
                            <p class="text-gray-600 text-sm mb-2">Chat instantané avec nos experts</p>
                            <a href="{{ $waUrl }}" target="_blank" class="text-green-600 hover:text-green-700 font-bold text-lg transition">
                                +{{ $whatsapp }}
                            </a>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-lg hover:shadow-md transition">
                        <div class="bg-blue-500 text-white rounded-full p-3 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 mb-1">Email</h3>
                            <p class="text-gray-600 text-sm mb-2">Envoyez-nous un message</p>
                            <a href="mailto:{{ $email }}" class="text-blue-600 hover:text-blue-700 font-bold text-lg transition">
                                {{ $email }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Formulaire de contact --}}
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Demande de devis</h2>
                    <form action="{{ route('contact') }}" method="POST" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nom" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nom" name="nom" required value="{{ old('nom') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                @error('nom')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="telephone" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Téléphone <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="telephone" name="telephone" placeholder="+226 XX XX XX XX" required value="{{ old('telephone') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                @error('telephone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sujet" class="block text-sm font-semibold text-gray-700 mb-1">
                                Sujet <span class="text-red-500">*</span>
                            </label>
                            <select id="sujet" name="sujet" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                <option value="">Sélectionnez un sujet</option>
                                <option value="devis">Demande de devis</option>
                                <option value="installation">Installation solaire</option>
                                <option value="maintenance">Maintenance/Dépannage</option>
                                <option value="information">Information produit</option>
                                <option value="autre">Autre</option>
                            </select>
                            @error('sujet')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-semibold text-gray-700 mb-1">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea id="message" name="message" rows="5" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                      placeholder="Décrivez votre besoin...">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition shadow-lg transform hover:scale-105 active:scale-95">
                            Envoyer la demande
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Localisation --}}
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Notre localisation</h2>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                @if($maps)
                    <iframe src="{{ $maps }}" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                @else
                    <div class="h-64 bg-gray-100 flex items-center justify-center text-gray-400">
                        Carte non configurée — ajoutez un lien Google Maps dans les Paramètres
                    </div>
                @endif
                <div class="p-6 bg-gradient-to-r from-green-600 to-blue-600 text-white">
                    <p class="font-semibold text-lg mb-1">📍 Jackson Energy International</p>
                    <p class="text-blue-100">{{ $address }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section Horaires --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Horaires d'ouverture</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold text-green-700 mb-4">🕐 Horaires</h3>
                    <p class="text-gray-700 text-lg">{{ $hours }}</p>
                </div>
                <div class="bg-gradient-to-br from-orange-50 to-amber-50 p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold text-orange-700 mb-4">🏖️ Urgences</h3>
                    <p class="text-gray-700 text-sm mt-2">Nos experts sont disponibles 24h/24 pour les urgences via WhatsApp</p>
                    <a href="{{ $waUrl }}" target="_blank" class="mt-3 inline-block bg-green-500 text-white px-4 py-2 rounded-lg font-semibold text-sm">
                        💬 WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'JACKSON ENERGY - Solutions Solaires au Burkina Faso' }}</title>
    <meta name="description" content="{{ $description ?? 'Jackson Energy International : Solutions solaires complètes, installation, maintenance et dépannage au Burkina Faso.' }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- HEADER FIXE --}}
    <header class="bg-white shadow-md sticky top-0 z-50">
        {{-- Barre de contact supérieure --}}
        <div class="bg-green-600 text-white py-2">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between text-xs sm:text-sm">
                <div class="flex items-center gap-2">
                    <span class="font-bold">JACKSON ENERGY</span>
                    <span class="hidden sm:inline">- International</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="tel:+22677126519" class="hover:underline flex items-center gap-1">
                        <span>📞</span>
                        <span class="hidden sm:inline">+226 77 12 65 19</span>
                    </a>
                    <a href="https://wa.me/22663952032" target="_blank" class="hover:underline flex items-center gap-1">
                        <span>💬</span>
                        <span class="hidden sm:inline">WhatsApp +226 63 95 20 32</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Navigation principale --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    {{-- Logo --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <div class="h-10 w-10 rounded-full bg-green-600 flex items-center justify-center text-white text-lg font-bold">
                                JE
                            </div>
                            <div class="hidden sm:block">
                                <div class="text-lg font-bold text-green-700">Jackson Energy</div>
                                <div class="text-xs text-gray-500">International</div>
                            </div>
                        </a>
                    </div>

                    {{-- Navigation desktop --}}
                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                        <a href="{{ route('home') }}" 
                           class="text-gray-700 hover:text-green-600 transition {{ request()->routeIs('home') ? 'text-green-600 font-semibold border-b-2 border-green-600 pb-1' : '' }}">
                            Accueil
                        </a>
                        <a href="{{ route('products.index') }}" 
                           class="text-gray-700 hover:text-green-600 transition {{ request()->routeIs('products.*') ? 'text-green-600 font-semibold border-b-2 border-green-600 pb-1' : '' }}">
                            Produits
                        </a>
                        <a href="{{ route('categories.index') }}" 
                           class="text-gray-700 hover:text-green-600 transition {{ request()->routeIs('categories.*') ? 'text-green-600 font-semibold border-b-2 border-green-600 pb-1' : '' }}">
                            Catégories
                        </a>
                        <a href="{{ route('contact') }}" 
                           class="text-gray-700 hover:text-green-600 transition {{ request()->routeIs('contact') ? 'text-green-600 font-semibold border-b-2 border-green-600 pb-1' : '' }}">
                            Contact
                        </a>
                    </nav>

                    {{-- Boutons d'action desktop --}}
                    <div class="hidden md:flex items-center gap-2">
                        <a href="tel:+22677126519" 
                           class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-md">
                            📞 Appeler
                        </a>
                        <a href="https://wa.me/22663952032" 
                           target="_blank"
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold text-sm transition shadow-md">
                            💬 WhatsApp
                        </a>
                    </div>

                    {{-- Menu burger mobile --}}
                    <button id="mobile-menu-button" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu mobile --}}
        <div id="mobile-menu" class="hidden md:hidden bg-white border-b border-gray-200 animate-slideDown">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('home') }}" 
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('home') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    Accueil
                </a>
                <a href="{{ route('products.index') }}" 
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('products.*') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    Produits
                </a>
                <a href="{{ route('categories.index') }}" 
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('categories.*') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    Catégories
                </a>
                <a href="{{ route('contact') }}" 
                   class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('contact') ? 'bg-green-50 text-green-600 font-semibold' : '' }}">
                    Contact
                </a>
                <div class="pt-2 space-y-2">
                    <a href="tel:+22677126519" 
                       class="block bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold text-center transition">
                        📞 Appeler +226 77 12 65 19
                    </a>
                    <a href="https://wa.me/22663952032" 
                       target="_blank"
                       class="block bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold text-center transition">
                        💬 WhatsApp +226 63 95 20 32
                    </a>
                </div>
            </div>
        </div>
    </header>

    {{-- CONTENU PRINCIPAL --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- FOOTER FIXE --}}
    <footer class="bg-blue-600 text-white mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- À propos --}}
                <div>
                    <h3 class="text-lg font-bold mb-4 text-yellow-300 uppercase tracking-wide">À PROPOS DE NOUS</h3>
                    <p class="text-sm text-blue-100 leading-relaxed">
                        Jackson Energy est une entreprise burkinabè spécialisée dans la fourniture et l'installation d'équipements solaires adaptés, pour les ménages et PME au Burkina Faso.
                    </p>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="text-lg font-bold mb-4 text-yellow-300 uppercase tracking-wide">CONTACTEZ-NOUS</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-300 text-lg">📧</span>
                            <span>Email: info@jacksonenergy.bf</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-300 text-lg">📞</span>
                            <span>Tél.: +226 77 12 65 19</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-yellow-300 text-lg">💬</span>
                            <span>WhatsApp: +226 63 95 20 32</span>
                        </li>
                    </ul>
                </div>

                {{-- Liens rapides --}}
                <div>
                    <h3 class="text-lg font-bold mb-4 text-yellow-300 uppercase tracking-wide">LIENS RAPIDES</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-yellow-300 transition">Accueil</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-yellow-300 transition">Produits</a></li>
                        <li><a href="{{ route('categories.index') }}" class="hover:text-yellow-300 transition">Catégories</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-yellow-300 transition">Contact</a></li>
                    </ul>
                </div>

                {{-- Suivez-nous --}}
                <div>
                    <h3 class="text-lg font-bold mb-4 text-yellow-300 uppercase tracking-wide">SUIVEZ-NOUS</h3>
                    <div class="flex gap-3 mb-6">
                        <a href="#" class="bg-green-500 rounded-full p-2 hover:bg-yellow-300 hover:text-blue-600 transition transform hover:scale-110" aria-label="Facebook" title="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22,12.07A10,10,0,1,0,12.07,22V14.47h-2V12.07h2V10.14c0-2,1-3,3.1-3a12.6,12.6,0,0,1,1.63.14v1.94h-.92c-.91,0-1.09.43-1.09,1.07v1.28h2.18l-.28,2.4H14.79v7.55A10,10,0,0,0,22,12.07Z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-green-500 rounded-full p-2 hover:bg-yellow-300 hover:text-blue-600 transition transform hover:scale-110" aria-label="Twitter" title="Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557a9.864 9.864 0 01-2.828.775 4.932 4.932 0 002.165-2.724c-.936.556-1.974.959-3.076 1.184a4.92 4.92 0 00-8.385 4.482c-4.087-.205-7.713-2.164-10.141-5.144a4.822 4.822 0 00-.665 2.475c0 1.708.87 3.215 2.188 4.098a4.904 4.904 0 01-2.229-.616v.062a4.927 4.927 0 003.946 4.832 4.996 4.996 0 01-2.224.084 4.927 4.927 0 004.604 3.419A9.868 9.868 0 010 21.543 13.912 13.912 0 007.548 24c9.142 0 14.307-7.721 14.307-14.417 0-.22-.006-.438-.017-.655A10.243 10.243 0 0024 4.557z"/>
                            </svg>
                        </a>
                        <a href="#" class="bg-green-500 rounded-full p-2 hover:bg-yellow-300 hover:text-blue-600 transition transform hover:scale-110" aria-label="Instagram" title="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.202 0 3.584.012 4.849.07 1.17.058 1.914.25 2.36.415a4.606 4.606 0 011.675 1.053 4.582 4.582 0 011.053 1.675c.165.446.357 1.19.415 2.36.058 1.266.07 1.647.07 4.849s-.012 3.584-.07 4.849c-.058 1.17-.25 1.914-.415 2.36-.241.632-.522 1.084-1.053 1.675a4.582 4.582 0 01-1.675 1.053c-.446.165-1.19.357-2.36.415-1.266.058-1.647.07-4.849.07s-3.584-.012-4.849-.07c-1.17-.058-1.914-.25-2.36-.415a4.606 4.606 0 01-1.675-1.053 4.582 4.582 0 01-1.053-1.675c-.165-.446-.357-1.19-.415-2.36C2.175 15.598 2.163 15.217 2.163 12S2.175 8.416 2.233 7.15c.058-1.17.25-1.914.415-2.36A4.609 4.609 0 013.7 3.115a4.582 4.582 0 011.675-1.053c.446-.165 1.19-.357 2.36-.415C8.416 2.175 8.798 2.163 12 2.163zm0-2.163C8.741 0 8.332.012 7.052.07 5.774.127 4.691.358 3.74.743c-.952.386-1.751.963-2.625 1.837S.386 2.788.743 3.74c.386.951.616 2.034.673 3.312C.988 8.332 1 8.741 1 12s-.012 3.668-.07 4.948c-.057 1.278-.287 2.361-.673 3.312-.357.952-.963 1.751-1.837 2.625S.386 21.212.743 20.26c-.386-.951-.616-2.034-.673-3.312C.012 15.668 0 15.259 0 12s.012-3.668.07-4.948c.057-1.278.287-2.361.673-3.312C.386 2.788.963 1.989 1.837 1.115S2.788.386 3.74.743c.951.386 2.034.616 3.312.673C8.332.012 8.741 0 12 0z"/>
                            </svg>
                        </a>
                    </div>
                    <div class="text-xs text-blue-100">
                        <p class="font-semibold">📍 LOCALISATION</p>
                        <p class="mt-1">Ouagadougou, Burkina Faso</p>
                    </div>
                </div>
            </div>

            {{-- Copyright --}}
            <div class="border-t border-blue-500 mt-8 pt-6 text-center text-sm text-blue-100">
                <p>&copy; {{ date('Y') }} Jackson Energy International. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    {{-- Script pour le menu mobile --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>

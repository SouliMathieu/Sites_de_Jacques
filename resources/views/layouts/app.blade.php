<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>JACKSON ENERGY - Énergie Solaire Burkina Faso</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-green-50">

    <!-- Barre de contact supérieure -->
    <div class="w-full bg-vert-burkina text-white flex items-center justify-between px-4 py-2 text-sm">
        <div class="flex gap-4 items-center">
            <span class="font-montserrat font-bold tracking-wider text-lg">JACKSON ENERGY</span>
        </div>
        <div class="flex gap-4 items-center">
            <a href="tel:+22677126519" class="hover:underline flex items-center gap-1">
                <span>&#128222;</span>
                <span>+22677126519</span>
            </a>
            <a href="https://wa.me/22663952032" target="_blank" class="hover:underline flex items-center gap-1">
                <span>WhatsApp</span>
                <span>+22663952032</span>
            </a>
        </div>
    </div>

    <!-- Header principal (logo + nav + boutons) -->
    <header class="flex items-center justify-between px-6 py-3 bg-white shadow">
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo/Logo.jpg') }}" alt="Logo Jackson Energy" class="h-14 w-14 rounded shadow" />
            <div>
                <span class="font-montserrat text-2xl font-bold text-vert-burkina">Jackson Energy</span>
                <span class="block text-gray-500 text-sm">International</span>
            </div>
        </div>

        <nav class="flex gap-8 items-center font-montserrat text-lg">
            <a href="{{ route('home') }}" class="text-vert-burkina font-semibold border-b-2 border-vert-burkina pb-1">Accueil</a>
            <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-vert-burkina">Produits</a>
            <a href="{{ route('categories.index') }}" class="text-gray-700 hover:text-vert-burkina">Catégories</a>
            <a href="{{ route('contact') }}" class="text-gray-700 hover:text-vert-burkina">Contact</a>
        </nav>

        <div class="flex gap-3">
            <a href="tel:+22677126519" class="bg-orange-burkina text-white py-2 px-4 rounded font-bold flex items-center gap-1 hover:bg-yellow-500 transition-all">
                <span>&#128222;</span> Appeler
            </a>
            <a href="https://wa.me/22663952032" target="_blank" class="bg-green-500 text-white py-2 px-4 rounded font-bold flex items-center gap-1 hover:bg-green-700 transition-all">
                <span>&#128172;</span> WhatsApp
            </a>
        </div>
    </header>

    {{-- NavBar - supprimée comme demandé --}}
    {{-- @include('layouts.navigation') --}}

    <main>
        @yield('content')
    </main>

    <!-- Footer personnalisé -->
    <footer class="bg-bleu-tech text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-5 gap-6 px-4">
            <!-- À propos de nous -->
            <div class="md:col-span-1">
                <h4 class="text-lg font-bold mb-3 tracking-widest">À PROPOS DE NOUS</h4>
                <p class="text-sm leading-relaxed">
                    Jackson Energy est une entreprise burkinabè spécialisée dans la fourniture et l'installation d’équipements solaires adaptés, pour les ménages et PME au Burkina Faso.
                </p>
            </div>

            <!-- Contactez-nous -->
            <div class="md:col-span-1">
                <h4 class="text-lg font-bold mb-3 tracking-widest">CONTACTEZ-NOUS</h4>
                <ul class="text-sm space-y-2">
                    <li>Email : info@jacksonenergy.bf</li>
                    <li>Tél. : +226 77 12 65 19</li>
                    <li>WhatsApp : +226 63 95 20 32</li>
                </ul>
            </div>

            <!-- Liens rapides -->
            <div class="md:col-span-1">
                <h4 class="text-lg font-bold mb-3 tracking-widest">LIENS RAPIDES</h4>
                <ul class="text-sm space-y-2">
                    <li><a href="{{ route('home') }}" class="hover:underline">Accueil</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:underline">Produits</a></li>
                    <li><a href="{{ route('categories.index') }}" class="hover:underline">Catégories</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:underline">Contact</a></li>
                </ul>
            </div>

            <!-- Localisation -->
            <div class="md:col-span-1">
                <h4 class="text-lg font-bold mb-3 tracking-widest">LOCALISATION</h4>
                <div class="rounded overflow-hidden shadow">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3979.847240514565!2d-1.5212256!3d12.3714323!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfad6eff7fd1641ef%3A0xe991a83d9b374264!2sOuagadougou!5e0!3m2!1sfr!2sbf!4v1615149550971!5m2!1sfr!2sbf"
                        width="100%" height="80" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <!-- Suivez-nous -->
            <div class="md:col-span-1 flex flex-col items-start">
                <h4 class="text-lg font-bold mb-3 tracking-widest">SUIVEZ-NOUS</h4>
                <div class="flex gap-3 mb-3">
                    <a href="#" class="bg-green-400 rounded-full p-2 hover:bg-yellow-200 transition" aria-label="Facebook">
                        <svg class="w-5 h-5" fill="white" viewBox="0 0 24 24"><path d="M22,12.07A10,10,0,1,0,12.07,22V14.47h-2V12.07h2V10.14c0-2,1-3,3.1-3a12.6,12.6,0,0,1,1.63.14v1.94h-.92c-.91,0-1.09.43-1.09,1.07v1.28h2.18l-.28,2.4H14.79v7.55A10,10,0,0,0,22,12.07Z"/></svg>
                    </a>
                    <a href="#" class="bg-green-400 rounded-full p-2 hover:bg-yellow-200 transition" aria-label="Twitter">
                        <svg class="w-5 h-5" fill="white" viewBox="0 0 24 24"><path d="M24 4.557a9.864 9.864 0 01-2.828.775 4.932 4.932 0 002.165-2.724c-.936.556-1.974.959-3.076 1.184a4.92 4.92 0 00-8.385 4.482c-4.087-.205-7.713-2.164-10.141-5.144a4.822 4.822 0 00-.665 2.475c0 1.708.87 3.215 2.188 4.098a4.904 4.904 0 01-2.229-.616v.062a4.927 4.927 0 003.946 4.832 4.996 4.996 0 01-2.224.084 4.927 4.927 0 004.604 3.419A9.868 9.868 0 010 21.543 13.912 13.912 0 007.548 24c9.142 0 14.307-7.721 14.307-14.417 0-.22-.006-.438-.017-.655A10.243 10.243 0 0024 4.557z"/></svg>
                    </a>
                    <a href="#" class="bg-green-400 rounded-full p-2 hover:bg-yellow-200 transition" aria-label="Instagram">
                        <svg class="w-5 h-5" fill="white" viewBox="0 0 24 24"><path d="M12 2.163c3.202 0 3.584.012 4.849.07 1.17.058 1.914.25 2.36.415a4.606 4.606 0 011.675 1.053 4.582 4.582 0 011.053 1.675c.165.446.357 1.19.415 2.36.058 1.266.07 1.647.07 4.849s-.012 3.584-.07 4.849c-.058 1.17-.25 1.914-.415 2.36-.241.632-.522 1.084-1.053 1.675a4.582 4.582 0 01-1.675 1.053c-.446.165-1.19.357-2.36.415-1.266.058-1.647.07-4.849.07s-3.584-.012-4.849-.07c-1.17-.058-1.914-.25-2.36-.415a4.606 4.606 0 01-1.675-1.053 4.582 4.582 0 01-1.053-1.675c-.165-.446-.357-1.19-.415-2.36C2.175 15.598 2.163 15.217 2.163 12S2.175 8.416 2.233 7.15c.058-1.17.25-1.914.415-2.36A4.609 4.609 0 013.7 3.115a4.582 4.582 0 011.675-1.053c.446-.165 1.19-.357 2.36-.415C8.416 2.175 8.798 2.163 12 2.163zm0-2.163C8.741 0 8.332.012 7.052.07 5.774.127 4.691.358 3.74.743c-.952.386-1.751.963-2.625 1.837S.386 2.788.743 3.74c.386.951.616 2.034.673 3.312C.988 8.332 1 8.741 1 12s-.012 3.668-.07 4.948c-.057 1.278-.287 2.361-.673 3.312-.357.952-.963 1.751-1.837 2.625S.386 21.212.743 20.26c-.386-.951-.616-2.034-.673-3.312C.012 15.668 0 15.259 0 12s.012-3.668.07-4.948c.057-1.278.287-2.361.673-3.312C.386 2.788.963 1.989 1.837 1.115S2.788.386 3.74.743c.951.386 2.034.616 3.312.673C8.332.012 8.741 0 12 0z"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="mt-6 text-center text-xs text-blue-100">
            &copy; 2025 Jackson Energy. Tous droits réservés.
        </div>
    </footer>

</body>
</html>

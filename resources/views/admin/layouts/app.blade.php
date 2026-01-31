<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration - Grossiste Ouaga International')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSS Dropzone LOCAL -->
    <link rel="stylesheet" href="{{ asset('assets/css/dropzone.min.css') }}" />
</head>
<body class="bg-gray-100">
    <!-- Navigation principale -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                        <div class="w-8 h-8 bg-vert-energie rounded-lg flex items-center justify-center text-white font-bold">GO</div>
                        <span class="ml-2 font-montserrat font-bold text-lg">Administration</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-vert-energie" target="_blank">🌐 Voir le site</a>
                    <span class="text-gray-600">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    {{-- Menu principal --}}
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-vert-energie text-white' : '' }}">
                        📊 Tableau de bord
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.categories.*') ? 'bg-vert-energie text-white' : '' }}">
                        📁 Catégories
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.products.*') ? 'bg-vert-energie text-white' : '' }}">
                        📦 Produits
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.orders.*') ? 'bg-vert-energie text-white' : '' }}">
                        📋 Commandes
                    </a>

                    {{-- ✅ NOUVEAU : Section Publicité --}}
                    <a href="{{ route('admin.ad-campaigns.index') }}" class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 {{ request()->routeIs('admin.ad-campaigns.*') ? 'bg-vert-energie text-white' : '' }}">
                        📢 Campagnes publicitaires
                    </a>

                    {{-- Séparateur visuel --}}
                    <div class="border-t border-gray-200 my-4"></div>

                    {{-- ✅ NOUVEAU : Liens externes --}}
                    <div class="px-4 py-2">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Plateformes externes</h4>
                    </div>

                    <a href="https://business.facebook.com/adsmanager/" target="_blank"
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                        <span class="flex-1">🎯 Meta Business Manager</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>

                    <a href="https://ads.google.com/" target="_blank"
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                        <span class="flex-1">🌐 Google Ads</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>

                    {{-- Séparateur visuel --}}
                    <div class="border-t border-gray-200 my-4"></div>

                    {{-- ✅ NOUVEAU : Liens utiles --}}
                    <div class="px-4 py-2">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Outils</h4>
                    </div>

                    <a href="https://analytics.google.com/" target="_blank"
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                        <span class="flex-1">📈 Google Analytics</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>

                    <a href="https://search.google.com/search-console/" target="_blank"
                       class="flex items-center px-4 py-2 text-gray-700 rounded-lg hover:bg-gray-100 group">
                        <span class="flex-1">🔍 Search Console</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>
                </div>

                {{-- ✅ NOUVEAU : Footer du sidebar avec info --}}
                <div class="px-4 py-4 mt-8 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 mb-2">Publicité en attente d'API</div>
                        <div class="text-xs text-gray-400">
                            <div class="flex items-center justify-center space-x-2">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
                                <span>Meta: En attente de vérification</span>
                            </div>
                            <div class="flex items-center justify-center space-x-2 mt-1">
                                <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                                <span>Google: Non éligible</span>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1 p-8">
            {{-- Messages de session --}}
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center">
                <span class="text-green-600 mr-2">✅</span>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
                <span class="text-red-600 mr-2">❌</span>
                {{ session('error') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6 flex items-center">
                <span class="text-yellow-600 mr-2">⚠️</span>
                {{ session('warning') }}
            </div>
            @endif

            @if(session('info'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6 flex items-center">
                <span class="text-blue-600 mr-2">ℹ️</span>
                {{ session('info') }}
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- JS Dropzone SIMPLE LOCAL -->
    <script src="{{ asset('assets/js/dropzone-simple.js') }}"></script>
    <script>
        console.log('✅ SimpleDropzone chargé avec succès');

        // ✅ NOUVEAU : Script pour les liens externes
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter une confirmation pour les liens externes
            const externalLinks = document.querySelectorAll('a[target="_blank"]');
            externalLinks.forEach(link => {
                // Ajouter un indicateur visuel au survol
                link.addEventListener('mouseenter', function() {
                    this.title = this.title || 'Lien externe - S\'ouvre dans un nouvel onglet';
                });
            });

            // Fonction utilitaire pour afficher des notifications
            window.showAdminNotification = function(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-100 text-green-700 border border-green-400' :
                    type === 'error' ? 'bg-red-100 text-red-700 border border-red-400' :
                    type === 'warning' ? 'bg-yellow-100 text-yellow-700 border border-yellow-400' :
                    'bg-blue-100 text-blue-700 border border-blue-400'
                }`;

                const icon = {
                    'success': '✅',
                    'error': '❌',
                    'warning': '⚠️',
                    'info': 'ℹ️'
                }[type] || 'ℹ️';

                notification.innerHTML = `
                    <div class="flex items-center">
                        <span class="mr-2">${icon}</span>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-lg">&times;</button>
                    </div>
                `;

                document.body.appendChild(notification);

                // Auto-remove après 5 secondes
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 5000);
            };
        });
    </script>

    @stack('scripts')
</body>
</html>

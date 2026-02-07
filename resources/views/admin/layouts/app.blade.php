<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration - Jackson Energy International')</title>
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
                        <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-red-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">JE</div>
                        <span class="ml-2 font-montserrat font-bold text-lg text-gray-900">Jackson Energy</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-green-600 transition-colors" target="_blank" title="Voir le site public">🌐 Voir le site</a>
                    <span class="text-gray-600 text-sm">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors text-sm font-medium">🚪 Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm min-h-screen border-r">
            <nav class="mt-6">
                <div class="px-4 space-y-2">
                    {{-- Menu principal --}}
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md' : '' }}">
                        <span class="text-lg mr-3">📊</span>
                        <span class="font-medium">Tableau de bord</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md' : '' }}">
                        <span class="text-lg mr-3">📁</span>
                        <span class="font-medium">Catégories</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md' : '' }}">
                        <span class="text-lg mr-3">📦</span>
                        <span class="font-medium">Produits</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md' : '' }}">
                        <span class="text-lg mr-3">📋</span>
                        <span class="font-medium">Commandes</span>
                    </a>

                    {{-- Séparateur visuel --}}
                    <div class="border-t border-gray-200 my-4"></div>

                    {{-- Liens externes --}}
                    <div class="px-4 py-2">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Plateformes externes</h4>
                    </div>

                    <a href="https://business.facebook.com/adsmanager/" target="_blank" rel="noopener noreferrer"
                       class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors group">
                        <span class="text-lg mr-3">🎯</span>
                        <span class="flex-1 font-medium">Meta Business</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>

                    <a href="https://ads.google.com/" target="_blank" rel="noopener noreferrer"
                       class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors group">
                        <span class="text-lg mr-3">🌐</span>
                        <span class="flex-1 font-medium">Google Ads</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>

                    {{-- Séparateur visuel --}}
                    <div class="border-t border-gray-200 my-4"></div>

                    {{-- Liens utiles --}}
                    <div class="px-4 py-2">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Outils</h4>
                    </div>

                    <a href="https://analytics.google.com/" target="_blank" rel="noopener noreferrer"
                       class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors group">
                        <span class="text-lg mr-3">📈</span>
                        <span class="flex-1 font-medium">Google Analytics</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>

                    <a href="https://search.google.com/search-console/" target="_blank" rel="noopener noreferrer"
                       class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors group">
                        <span class="text-lg mr-3">🔍</span>
                        <span class="flex-1 font-medium">Search Console</span>
                        <span class="text-xs text-gray-400 group-hover:text-gray-600 transition-colors">↗</span>
                    </a>
                </div>

                {{-- Footer du sidebar avec info --}}
                <div class="px-4 py-4 mt-8 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-xs text-gray-500 mb-3 font-medium">État des APIs</div>
                        <div class="text-xs space-y-2">
                            <div class="flex items-center justify-center space-x-2 p-2 bg-yellow-50 rounded-lg">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                                <span class="text-gray-600">Meta: En attente</span>
                            </div>
                            <div class="flex items-center justify-center space-x-2 p-2 bg-red-50 rounded-lg">
                                <span class="w-2 h-2 bg-red-400 rounded-full"></span>
                                <span class="text-gray-600">Google: Non éligible</span>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Contenu principal -->
        <div class="flex-1">
            {{-- Messages de session --}}
            @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg shadow-sm flex items-start space-x-3 animate-slideInDown">
                <span class="text-lg mt-0.5">✅</span>
                <div class="flex-1">
                    <p class="font-medium">Succès</p>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">&times;</button>
            </div>
            @endif

            @if(session('error'))
            <div class="m-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg shadow-sm flex items-start space-x-3 animate-slideInDown">
                <span class="text-lg mt-0.5">❌</span>
                <div class="flex-1">
                    <p class="font-medium">Erreur</p>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
            </div>
            @endif

            @if(session('warning'))
            <div class="m-6 p-4 bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg shadow-sm flex items-start space-x-3 animate-slideInDown">
                <span class="text-lg mt-0.5">⚠️</span>
                <div class="flex-1">
                    <p class="font-medium">Attention</p>
                    <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-yellow-600 hover:text-yellow-800">&times;</button>
            </div>
            @endif

            @if(session('info'))
            <div class="m-6 p-4 bg-blue-50 border border-blue-300 text-blue-800 rounded-lg shadow-sm flex items-start space-x-3 animate-slideInDown">
                <span class="text-lg mt-0.5">ℹ️</span>
                <div class="flex-1">
                    <p class="font-medium">Information</p>
                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-800">&times;</button>
            </div>
            @endif

            {{-- Contenu --}}
            <div class="p-8">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- JS Dropzone LOCAL -->
    <script src="{{ asset('assets/js/dropzone-simple.js') }}"></script>
    <script>
        console.log('✅ SimpleDropzone chargé avec succès');

        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter une confirmation pour les liens externes
            const externalLinks = document.querySelectorAll('a[target="_blank"]');
            externalLinks.forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.title = this.title || 'Lien externe - S\'ouvre dans un nouvel onglet';
                });
            });

            // Fonction utilitaire pour afficher des notifications
            window.showAdminNotification = function(message, type = 'info') {
                const notification = document.createElement('div');
                const bgClass = {
                    'success': 'bg-green-50 border border-green-300 text-green-800',
                    'error': 'bg-red-50 border border-red-300 text-red-800',
                    'warning': 'bg-yellow-50 border border-yellow-300 text-yellow-800',
                    'info': 'bg-blue-50 border border-blue-300 text-blue-800'
                }[type] || 'bg-blue-50 border border-blue-300 text-blue-800';

                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm animate-slideInDown ${bgClass}`;

                const icon = {
                    'success': '✅',
                    'error': '❌',
                    'warning': '⚠️',
                    'info': 'ℹ️'
                }[type] || 'ℹ️';

                notification.innerHTML = `
                    <div class="flex items-start space-x-3">
                        <span class="text-lg">${icon}</span>
                        <div class="flex-1">
                            <p class="text-sm font-medium">${message}</p>
                        </div>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-lg leading-none">&times;</button>
                    </div>
                `;

                document.body.appendChild(notification);

                // Auto-remove après 5 secondes
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.style.animation = 'slideOutUp 0.3s ease-out forwards';
                        setTimeout(() => notification.remove(), 300);
                    }
                }, 5000);
            };

            // Ajouter l'animation CSS si elle n'existe pas
            if (!document.querySelector('style[data-admin-animations]')) {
                const style = document.createElement('style');
                style.setAttribute('data-admin-animations', 'true');
                style.innerHTML = `
                    @keyframes slideInDown {
                        from {
                            opacity: 0;
                            transform: translateY(-20px);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    @keyframes slideOutUp {
                        from {
                            opacity: 1;
                            transform: translateY(0);
                        }
                        to {
                            opacity: 0;
                            transform: translateY(-20px);
                        }
                    }

                    .animate-slideInDown {
                        animation: slideInDown 0.3s ease-out;
                    }

                    .animate-pulse {
                        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
                    }

                    @keyframes pulse {
                        0%, 100% {
                            opacity: 1;
                        }
                        50% {
                            opacity: .5;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

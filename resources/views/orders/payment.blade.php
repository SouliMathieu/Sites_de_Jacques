@extends('layouts.public')

@section('title', 'Paiement - Commande ' . $order->order_number)

@section('content')
{{-- Hero Header --}}
<div class="bg-gradient-to-r from-green-600 to-blue-600 py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-full mb-4">
                <span class="text-5xl">💳</span>
            </div>
            <h1 class="text-4xl font-montserrat font-bold text-white mb-3">
                Finaliser votre paiement
            </h1>
            <p class="text-green-100 text-lg mb-2">
                Commande <span class="bg-white/20 px-4 py-1 rounded-full font-bold">{{ $order->order_number }}</span>
            </p>
            <p class="text-green-100">
                Montant total : <span class="text-2xl font-bold text-white">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
            </p>
        </div>
    </div>
</div>

{{-- Progress Steps --}}
<div class="bg-white border-b shadow-sm">
    <div class="container mx-auto px-4 py-4">
        <div class="max-w-5xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full font-bold text-sm">✓</div>
                    <div class="ml-2">
                        <p class="text-xs font-semibold text-green-600">Commande créée</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-green-600 mx-2"></div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-8 h-8 bg-green-600 text-white rounded-full font-bold">2</div>
                    <div class="ml-2">
                        <p class="text-xs font-semibold text-green-600">Paiement</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-8 h-8 bg-gray-300 text-gray-600 rounded-full font-bold text-sm">3</div>
                    <div class="ml-2">
                        <p class="text-xs font-semibold text-gray-500">Confirmation</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Content --}}
<div class="container mx-auto px-4 py-12">
    <div class="max-w-6xl mx-auto">
        <div class="grid lg:grid-cols-5 gap-8">
            {{-- Colonne principale - Instructions (3/5) --}}
            <div class="lg:col-span-3 space-y-6">
                {{-- Instructions de paiement --}}
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-blue-500 p-6">
                        <h2 class="text-2xl font-montserrat font-bold text-white flex items-center">
                            <span class="text-3xl mr-3">💳</span>
                            Instructions de paiement
                        </h2>
                    </div>

                    <div class="p-8">
                        @if($order->payment_method == 'orange_money')
                        {{-- Orange Money --}}
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-2xl mr-4 shadow-lg">
                                    OM
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Orange Money</h3>
                                    <p class="text-gray-600">Paiement mobile sécurisé</p>
                                </div>
                            </div>

                            {{-- Option 1: MaxIt App --}}
                            <div class="bg-orange-50 border-2 border-orange-300 rounded-xl p-6 hover:shadow-lg transition">
                                <h4 class="font-bold text-orange-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">📱</span>
                                    Méthode 1 : Via l'application MaxIt (Recommandé)
                                </h4>
                                
                                <button onclick="openMaxItApp()"
                                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-4 px-6 rounded-lg font-bold text-lg shadow-md hover:shadow-xl transition transform hover:scale-105 mb-4">
                                    📱 Ouvrir MaxIt maintenant
                                </button>

                                <div class="bg-white border border-orange-200 rounded-lg p-4">
                                    <p class="font-semibold text-orange-800 mb-3">📋 Instructions détaillées :</p>
                                    <ol class="space-y-2 text-sm text-gray-700">
                                        <li class="flex items-start">
                                            <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">1</span>
                                            <span>L'application MaxIt s'ouvrira automatiquement</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">2</span>
                                            <span>Allez dans <strong>"Transfert"</strong> → <strong>"Envoi d'argent"</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">3</span>
                                            <span>Numéro bénéficiaire : <strong class="bg-orange-100 px-2 py-1 rounded">65 03 37 00</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">4</span>
                                            <span>Montant : <strong class="bg-orange-100 px-2 py-1 rounded">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">5</span>
                                            <span>Confirmez avec votre code PIN</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">6</span>
                                            <span>Notez le numéro de transaction reçu par SMS</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>

                            {{-- Option 2: Code USSD --}}
                            <div class="bg-white border-2 border-orange-200 rounded-xl p-6 hover:shadow-lg transition">
                                <h4 class="font-bold text-orange-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">📞</span>
                                    Méthode 2 : Via code USSD (Alternative)
                                </h4>
                                
                                <button onclick="openOrangeUSSD()"
                                        class="w-full bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white py-4 px-6 rounded-lg font-bold text-lg shadow-md hover:shadow-xl transition transform hover:scale-105 mb-4">
                                    📞 Composer le code USSD
                                </button>

                                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                                    <p class="font-semibold text-orange-800 mb-2">Code rapide à composer :</p>
                                    <div class="bg-white border-2 border-orange-300 rounded-lg p-4 font-mono text-center text-xl font-bold text-orange-700 mb-3">
                                        *144*2*1*65033700*{{ number_format($order->total_amount, 0, '', '') }}#
                                    </div>
                                    <p class="text-sm text-orange-700">
                                        💡 Ou composez <strong>*144#</strong> et suivez les instructions étape par étape
                                    </p>
                                </div>
                            </div>

                            {{-- Option 3: Paiement à la livraison --}}
                            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6">
                                <h4 class="font-bold text-blue-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">🚚</span>
                                    Méthode 3 : Paiement Orange Money à la livraison
                                </h4>
                                <p class="text-sm text-blue-700 mb-4">
                                    Vous préférez payer directement à la livraison ? Notre livreur vous assistera pour la transaction Orange Money.
                                </p>
                                <form action="{{ route('orders.payment-at-delivery', $order) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_at_delivery" value="1">
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-3 px-6 rounded-lg font-bold shadow-md hover:shadow-xl transition transform hover:scale-105">
                                        <span class="mr-2">🚚</span> Choisir paiement à la livraison
                                    </button>
                                </form>
                            </div>
                        </div>

                        @elseif($order->payment_method == 'moov_money')
                        {{-- Moov Money --}}
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-2xl mr-4 shadow-lg">
                                    MM
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Moov Money</h3>
                                    <p class="text-gray-600">Paiement mobile sécurisé</p>
                                </div>
                            </div>

                            {{-- Option 1: Moov Money App --}}
                            <div class="bg-blue-50 border-2 border-blue-300 rounded-xl p-6 hover:shadow-lg transition">
                                <h4 class="font-bold text-blue-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">📱</span>
                                    Méthode 1 : Via l'application Moov Money (Recommandé)
                                </h4>
                                
                                <button onclick="openMoovApp()"
                                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 px-6 rounded-lg font-bold text-lg shadow-md hover:shadow-xl transition transform hover:scale-105 mb-4">
                                    📱 Ouvrir Moov Money maintenant
                                </button>

                                <div class="bg-white border border-blue-200 rounded-lg p-4">
                                    <p class="font-semibold text-blue-800 mb-3">📋 Instructions détaillées :</p>
                                    <ol class="space-y-2 text-sm text-gray-700">
                                        <li class="flex items-start">
                                            <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">1</span>
                                            <span>L'application Moov Money s'ouvrira automatiquement</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">2</span>
                                            <span>Allez dans <strong>"Transfert"</strong> → <strong>"Envoi d'argent"</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">3</span>
                                            <span>Numéro bénéficiaire : <strong class="bg-blue-100 px-2 py-1 rounded">70 10 39 93</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">4</span>
                                            <span>Montant : <strong class="bg-blue-100 px-2 py-1 rounded">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">5</span>
                                            <span>Confirmez avec votre code PIN</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">6</span>
                                            <span>Notez le numéro de transaction reçu par SMS</span>
                                        </li>
                                    </ol>
                                </div>
                            </div>

                            {{-- Option 2: Code USSD --}}
                            <div class="bg-white border-2 border-blue-200 rounded-xl p-6 hover:shadow-lg transition">
                                <h4 class="font-bold text-blue-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">📞</span>
                                    Méthode 2 : Via code USSD (Alternative)
                                </h4>
                                
                                <button onclick="openMoovUSSD()"
                                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white py-4 px-6 rounded-lg font-bold text-lg shadow-md hover:shadow-xl transition transform hover:scale-105 mb-4">
                                    📞 Composer le code USSD
                                </button>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <p class="font-semibold text-blue-800 mb-2">Code rapide à composer :</p>
                                    <div class="bg-white border-2 border-blue-300 rounded-lg p-4 font-mono text-center text-xl font-bold text-blue-700 mb-3">
                                        *555*2*1*70103993*{{ number_format($order->total_amount, 0, '', '') }}#
                                    </div>
                                    <p class="text-sm text-blue-700">
                                        💡 Ou composez <strong>*555#</strong> et suivez les instructions étape par étape
                                    </p>
                                </div>
                            </div>

                            {{-- Option 3: Paiement à la livraison --}}
                            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6">
                                <h4 class="font-bold text-green-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">🚚</span>
                                    Méthode 3 : Paiement Moov Money à la livraison
                                </h4>
                                <p class="text-sm text-green-700 mb-4">
                                    Vous préférez payer directement à la livraison ? Notre livreur vous assistera pour la transaction Moov Money.
                                </p>
                                <form action="{{ route('orders.payment-at-delivery', $order) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_at_delivery" value="1">
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-3 px-6 rounded-lg font-bold shadow-md hover:shadow-xl transition transform hover:scale-105">
                                        <span class="mr-2">🚚</span> Choisir paiement à la livraison
                                    </button>
                                </form>
                            </div>
                        </div>

                        @elseif($order->payment_method == 'bank_transfer')
                        {{-- Virement bancaire --}}
                        <div class="space-y-6">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center text-white text-3xl mr-4 shadow-lg">
                                    🏦
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Virement bancaire</h3>
                                    <p class="text-gray-600">Transfert bancaire sécurisé</p>
                                </div>
                            </div>

                            {{-- Option 1: UBA Mobile App --}}
                            <div class="bg-green-50 border-2 border-green-300 rounded-xl p-6 hover:shadow-lg transition">
                                <h4 class="font-bold text-green-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">📱</span>
                                    Méthode 1 : Via l'application UBA Mobile (Recommandé)
                                </h4>
                                
                                <button onclick="openUBAApp()"
                                        class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-4 px-6 rounded-lg font-bold text-lg shadow-md hover:shadow-xl transition transform hover:scale-105 mb-4">
                                    📱 Ouvrir UBA Mobile maintenant
                                </button>

                                <div class="bg-white border border-green-200 rounded-lg p-4">
                                    <p class="font-semibold text-green-800 mb-3">📋 Instructions détaillées :</p>
                                    <ol class="space-y-2 text-sm text-gray-700">
                                        <li class="flex items-start">
                                            <span class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">1</span>
                                            <span>Ouvrez l'application UBA Mobile</span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">2</span>
                                            <span>Allez dans <strong>"Transfert"</strong> → <strong>"Vers compte UBA"</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">3</span>
                                            <span>Numéro de compte : <strong class="bg-green-100 px-2 py-1 rounded">410730007217</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">4</span>
                                            <span>Bénéficiaire : <strong>Mr ZIDA ABDOULAYE</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">5</span>
                                            <span>Montant : <strong class="bg-green-100 px-2 py-1 rounded">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></span>
                                        </li>
                                        <li class="flex items-start">
                                            <span class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-3 mt-0.5 flex-shrink-0">6</span>
                                            <span>Référence : <strong>{{ $order->order_number }}</strong></span>
                                        </li>
                                    </ol>
                                </div>
                            </div>

                            {{-- Option 2: Coordonnées bancaires --}}
                            <div class="bg-white border-2 border-green-200 rounded-xl p-6">
                                <h4 class="font-bold text-green-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">🏦</span>
                                    Méthode 2 : Coordonnées bancaires (Alternative)
                                </h4>
                                
                                <div class="bg-green-50 border border-green-200 rounded-lg p-6 space-y-3">
                                    <div class="flex justify-between items-start py-2 border-b border-green-200">
                                        <span class="font-semibold text-gray-700">🏦 Banque :</span>
                                        <span class="font-bold text-gray-900">UBA Burkina Faso</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2 border-b border-green-200">
                                        <span class="font-semibold text-gray-700">👤 Titulaire :</span>
                                        <span class="font-bold text-gray-900">Mr ZIDA ABDOULAYE</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2 border-b border-green-200">
                                        <span class="font-semibold text-gray-700">💳 Compte :</span>
                                        <span class="font-mono font-bold text-green-700 bg-white px-3 py-1 rounded">410730007217</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2 border-b border-green-200">
                                        <span class="font-semibold text-gray-700">💰 Montant :</span>
                                        <span class="font-bold text-green-600 text-lg">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2">
                                        <span class="font-semibold text-gray-700">📋 Référence :</span>
                                        <span class="font-mono font-bold text-gray-900 bg-yellow-100 px-3 py-1 rounded">{{ $order->order_number }}</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mt-4">
                                    📸 Après le virement, envoyez-nous le reçu par WhatsApp ou email pour confirmation rapide.
                                </p>
                            </div>

                            {{-- Option 3: Paiement à la livraison --}}
                            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6">
                                <h4 class="font-bold text-yellow-700 mb-3 text-lg flex items-center">
                                    <span class="text-2xl mr-2">🚚</span>
                                    Méthode 3 : Paiement à la livraison
                                </h4>
                                <p class="text-sm text-yellow-700 mb-4">
                                    Vous préférez régler à la livraison ? Payez par virement, Mobile Money ou espèces selon votre convenance.
                                </p>
                                <form action="{{ route('orders.payment-at-delivery', $order) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="payment_at_delivery" value="1">
                                    <button type="submit" 
                                            class="w-full bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white py-3 px-6 rounded-lg font-bold shadow-md hover:shadow-xl transition transform hover:scale-105">
                                        <span class="mr-2">🚚</span> Choisir paiement à la livraison
                                    </button>
                                </form>
                            </div>
                        </div>

                        @elseif($order->payment_method == 'cash_on_delivery')
                        {{-- Paiement à la livraison --}}
                        <div class="bg-gray-50 border-2 border-gray-300 rounded-xl p-8">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white text-3xl mr-4 shadow-lg">
                                    💵
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Paiement à la livraison</h3>
                                    <p class="text-gray-600">Espèces ou Mobile Money</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg p-6 space-y-4">
                                <h4 class="font-bold text-gray-800 mb-4">📋 Informations importantes :</h4>
                                <ul class="space-y-3">
                                    <li class="flex items-start">
                                        <span class="text-green-600 text-xl mr-3">✓</span>
                                        <span class="text-gray-700">Préparez le montant exact : <strong class="bg-yellow-100 px-3 py-1 rounded font-bold text-lg">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 text-xl mr-3">✓</span>
                                        <span class="text-gray-700">Le paiement se fera <strong>en espèces ou Mobile Money</strong> lors de la livraison</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 text-xl mr-3">✓</span>
                                        <span class="text-gray-700">Assurez-vous d'être disponible à l'adresse indiquée</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 text-xl mr-3">✓</span>
                                        <span class="text-gray-700">Notre livreur vous contactera <strong>avant la livraison</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="text-green-600 text-xl mr-3">✓</span>
                                        <span class="text-gray-700">Un reçu officiel vous sera remis après paiement</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-700">
                                    💡 <strong>Astuce :</strong> Pour plus de rapidité, vous pouvez préparer le montant exact en Mobile Money (Orange Money ou Moov Money).
                                </p>
                            </div>
                        </div>
                        @endif

                        {{-- Formulaire de confirmation --}}
                        @if($order->payment_method !== 'cash_on_delivery')
                        <form method="POST" action="{{ route('orders.confirm-payment', $order) }}" class="mt-8" id="paymentForm">
                            @csrf

                            @if(in_array($order->payment_method, ['orange_money', 'moov_money', 'bank_transfer']))
                            <div class="bg-white border-2 border-green-300 rounded-xl p-6">
                                <label for="payment_reference" class="block text-sm font-bold text-gray-700 mb-3">
                                    @if($order->payment_method == 'bank_transfer')
                                        🔖 Numéro de référence du virement <span class="text-red-500">*</span>
                                    @else
                                        💳 Numéro de transaction reçu par SMS <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input type="text" 
                                       id="payment_reference" 
                                       name="payment_reference" 
                                       required 
                                       placeholder="Ex: MP240625.1234.A12345"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-lg font-mono">
                                <p class="text-sm text-gray-600 mt-2 flex items-center">
                                    <span class="mr-2">💡</span>
                                    Ce numéro nous permet de vérifier et valider rapidement votre paiement
                                </p>
                            </div>
                            @else
                            <input type="hidden" name="payment_reference" value="CASH_{{ $order->order_number }}">
                            @endif

                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-5 px-8 rounded-xl font-bold text-xl shadow-lg hover:shadow-2xl transition transform hover:scale-105 mt-6">
                                <span class="mr-2">✅</span> Confirmer le paiement
                            </button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('orders.confirm-payment', $order) }}" class="mt-8">
                            @csrf
                            <input type="hidden" name="payment_reference" value="CASH_{{ $order->order_number }}">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-5 px-8 rounded-xl font-bold text-xl shadow-lg hover:shadow-2xl transition transform hover:scale-105">
                                <span class="mr-2">✅</span> Confirmer la commande
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Contact d'urgence --}}
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-xl p-6 shadow-lg">
                    <h3 class="font-bold text-yellow-800 mb-3 text-lg flex items-center">
                        <span class="text-2xl mr-2">💡</span>
                        Besoin d'aide ?
                    </h3>
                    <p class="text-sm text-yellow-700 mb-4">Notre équipe est disponible pour vous assister :</p>
                    <div class="grid md:grid-cols-2 gap-3">
                        <a href="tel:+22665033700" 
                           class="flex items-center justify-center bg-white hover:bg-yellow-50 border-2 border-yellow-300 text-yellow-700 font-bold py-3 px-4 rounded-lg transition">
                            <span class="text-2xl mr-2">📞</span> +226 65 03 37 00
                        </a>
                        <a href="https://wa.me/22665033700?text=Bonjour, j'ai besoin d'aide pour ma commande {{ $order->order_number }}" 
                           target="_blank"
                           class="flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition shadow-md">
                            <span class="text-2xl mr-2">💬</span> WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            {{-- Colonne latérale - Résumé (2/5) --}}
            <div class="lg:col-span-2">
                <div class="sticky top-24 space-y-6">
                    {{-- Résumé de la commande --}}
                    <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-blue-500">
                        <h2 class="text-xl font-montserrat font-bold mb-6 text-gray-900 flex items-center">
                            <span class="text-2xl mr-2">📋</span> Récapitulatif
                        </h2>

                        {{-- Informations client --}}
                        <div class="border-b pb-4 mb-4">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">👤</span> Client
                            </h3>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-900 font-semibold">{{ $order->customer_name }}</p>
                                <p class="text-gray-600 flex items-center">
                                    <span class="mr-2">📞</span> {{ $order->customer_phone }}
                                </p>
                                @if($order->customer_email)
                                <p class="text-gray-600 flex items-center">
                                    <span class="mr-2">✉️</span> {{ $order->customer_email }}
                                </p>
                                @endif
                                @if($order->customer_company)
                                <p class="text-gray-600 flex items-center">
                                    <span class="mr-2">🏢</span> {{ $order->customer_company }}
                                </p>
                                @endif
                            </div>
                        </div>

                        {{-- Produits --}}
                        <div class="border-b pb-4 mb-4">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">🛍️</span> Produits ({{ $order->orderItems->count() }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($order->orderItems as $item)
                                <div class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                    <img src="{{ $item->product->first_image ?? '/images/placeholder.jpg' }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-12 h-12 object-cover rounded-lg border-2 border-gray-200">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-900 truncate">{{ $item->product_name }}</p>
                                        <p class="text-xs text-gray-600">
                                            {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                                        </p>
                                    </div>
                                    <p class="font-bold text-sm text-green-600 whitespace-nowrap">
                                        {{ number_format($item->total_price, 0, ',', ' ') }} F
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Livraison --}}
                        <div class="border-b pb-4 mb-4">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center">
                                <span class="mr-2">🚚</span> Livraison
                            </h3>
                            <div class="text-sm space-y-1">
                                <p class="text-gray-900">{{ $order->delivery_address }}</p>
                                <p class="text-gray-600">{{ $order->delivery_city }}</p>
                                <p class="text-gray-600 flex items-center">
                                    <span class="mr-2">📞</span> {{ $order->delivery_phone }}
                                </p>
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-lg p-4">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total à payer :</span>
                                <span class="text-3xl font-bold text-green-600">
                                    {{ number_format($order->total_amount, 0, ',', ' ') }} <span class="text-lg">FCFA</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Garanties --}}
                    <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl shadow-lg p-6 border-2 border-green-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <span class="text-2xl mr-2">🛡️</span> Paiement sécurisé
                        </h3>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Transactions 100% sécurisées</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Vérification immédiate</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Support client réactif</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span class="text-gray-700">Livraison garantie</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Détection du type d'appareil
function isMobileDevice() {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// Fonction pour ouvrir MaxIt (Orange)
function openMaxItApp() {
    if (isMobileDevice()) {
        const maxitUrl = 'maxit://transfer?number=65033700&amount={{ number_format($order->total_amount, 0, "", "") }}';
        const fallbackUrl = 'https://play.google.com/store/apps/details?id=com.orange.maxit.bf';
        
        window.location.href = maxitUrl;
        
        setTimeout(() => {
            if (confirm('MaxIt ne semble pas installé. Voulez-vous le télécharger ?')) {
                window.open(fallbackUrl, '_blank');
            }
        }, 2500);
        
        alert('Si MaxIt s\'ouvre :\n1. Allez dans Transfert → Envoi d\'argent\n2. Numéro: 65033700\n3. Montant: {{ number_format($order->total_amount, 0, ",", " ") }} FCFA');
    } else {
        alert('⚠️ Cette fonctionnalité fonctionne uniquement sur mobile.\n\nUtilisez le code USSD comme alternative.');
    }
}

// Fonction pour ouvrir l'app Moov Money
function openMoovApp() {
    if (isMobileDevice()) {
        const moovUrl = 'moovmoney://transfer?number=70103993&amount={{ number_format($order->total_amount, 0, "", "") }}';
        const fallbackUrl = 'https://play.google.com/store/apps/details?id=com.tlc.onatel.customer';
        
        window.location.href = moovUrl;
        
        setTimeout(() => {
            if (confirm('Moov Money ne semble pas installé. Voulez-vous le télécharger ?')) {
                window.open(fallbackUrl, '_blank');
            }
        }, 2500);
        
        alert('Si Moov Money s\'ouvre :\n1. Allez dans Transfert → Envoi d\'argent\n2. Numéro: 70103993\n3. Montant: {{ number_format($order->total_amount, 0, ",", " ") }} FCFA');
    } else {
        alert('⚠️ Cette fonctionnalité fonctionne uniquement sur mobile.\n\nUtilisez le code USSD comme alternative.');
    }
}

// Fonction pour ouvrir UBA Mobile App
function openUBAApp() {
    if (isMobileDevice()) {
        const ubaUrl = 'uba://transfer?account=410730007217&amount={{ number_format($order->total_amount, 0, "", "") }}&reference={{ $order->order_number }}';
        const fallbackUrl = 'https://play.google.com/store/apps/details?id=com.uba.mobile';
        
        window.location.href = ubaUrl;
        
        setTimeout(() => {
            if (confirm('UBA Mobile ne semble pas installé. Voulez-vous le télécharger ?')) {
                window.open(fallbackUrl, '_blank');
            }
        }, 2500);
        
        alert('Si UBA Mobile s\'ouvre :\n1. Transfert → Vers compte UBA\n2. Compte: 410730007217\n3. Bénéficiaire: Mr ZIDA ABDOULAYE\n4. Montant: {{ number_format($order->total_amount, 0, ",", " ") }} FCFA\n5. Référence: {{ $order->order_number }}');
    } else {
        alert('⚠️ Cette fonctionnalité fonctionne uniquement sur mobile.\n\nUtilisez les coordonnées bancaires comme alternative.');
    }
}

// Fonction pour ouvrir le code USSD Orange
function openOrangeUSSD() {
    const ussdCode = '*144*2*1*65033700*{{ number_format($order->total_amount, 0, "", "") }}#';
    
    if (isMobileDevice()) {
        window.location.href = `tel:${ussdCode}`;
    } else {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(ussdCode).then(() => {
                alert(`✅ Code copié dans le presse-papiers !\n\nComposez ce code sur votre téléphone Orange :\n${ussdCode}`);
            });
        } else {
            alert(`Composez ce code sur votre téléphone Orange :\n${ussdCode}`);
        }
    }
}

// Fonction pour ouvrir le code USSD Moov
function openMoovUSSD() {
    const ussdCode = '*555*2*1*70103993*{{ number_format($order->total_amount, 0, "", "") }}#';
    
    if (isMobileDevice()) {
        window.location.href = `tel:${ussdCode}`;
    } else {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(ussdCode).then(() => {
                alert(`✅ Code copié dans le presse-papiers !\n\nComposez ce code sur votre téléphone Moov :\n${ussdCode}`);
            });
        } else {
            alert(`Composez ce code sur votre téléphone Moov :\n${ussdCode}`);
        }
    }
}

// Validation du formulaire
const paymentForm = document.getElementById('paymentForm');
if (paymentForm) {
    paymentForm.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2">⏳</span> Vérification en cours...';
        }
    });
}

console.log('✅ Page paiement initialisée');
</script>
@endpush

@endsection

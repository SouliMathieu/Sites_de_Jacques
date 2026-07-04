@extends('layouts.app')

@section('title', 'Commande confirmée - ' . $order->order_number)

@section('content')
@php
    $phone    = \App\Models\SiteSetting::get('phone', '+226 77 12 65 19');
    $whatsapp = \App\Models\SiteSetting::get('whatsapp', '22663952032');
    $email    = \App\Models\SiteSetting::get('email', 'info@jacksonenergy.bf');
    $telUrl   = 'tel:' . preg_replace('/\s+/', '', $phone);
    $waUrl    = 'https://wa.me/' . $whatsapp;
@endphp
{{-- Hero Header Success --}}
<div class="bg-gradient-to-r from-green-500 to-emerald-600 py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            {{-- Animation checkmark --}}
            <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full mb-6 animate-bounce">
                <span class="text-6xl">✅</span>
            </div>
            
            <h1 class="text-4xl font-montserrat font-bold text-white mb-4">
                Merci {{ $order->customer_name }} !
            </h1>
            <p class="text-xl text-green-100 mb-6">
                Votre commande a été confirmée avec succès
            </p>
            <div class="inline-block bg-white/20 backdrop-blur-sm rounded-lg px-6 py-3">
                <p class="text-sm text-green-100 mb-1">Numéro de commande</p>
                <p class="text-2xl font-mono font-bold text-white">{{ $order->order_number }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Message adaptatif selon le mode de paiement --}}
<div class="container mx-auto px-4 -mt-8">
    <div class="max-w-4xl mx-auto">
        @if($order->payment_method === 'cash_on_delivery')
            {{-- Espèces à la livraison --}}
            <div class="bg-white rounded-xl shadow-2xl p-8 mb-8 border-l-4 border-yellow-500">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center text-3xl mr-6">
                        💰
                    </div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">Paiement en espèces à la livraison</h3>
                        <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                            <p class="text-yellow-900 font-semibold text-lg mb-2">
                                💵 Montant à préparer : <span class="text-2xl font-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                            </p>
                            <p class="text-yellow-800 text-sm">
                                Merci de préparer le montant exact si possible
                            </p>
                        </div>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span>Notre livreur vous contactera avant la livraison</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span>Vous recevrez un reçu officiel après paiement</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-600 mr-2">✓</span>
                                <span>Vous pouvez aussi payer par Mobile Money à la livraison</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        @elseif($order->payment_method === 'orange_money')
            @if($order->payment_status === 'paid')
                {{-- Orange Money payé --}}
                <div class="bg-white rounded-xl shadow-2xl p-8 mb-8 border-l-4 border-green-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-3xl mr-6">
                            ✅
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Paiement Orange Money confirmé</h3>
                            <div class="bg-green-50 rounded-lg p-4 mb-4">
                                <p class="text-green-900 font-semibold text-lg">
                                    ✅ Paiement de <span class="text-xl font-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span> reçu avec succès
                                </p>
                            </div>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Votre commande est en cours de préparation</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Vous recevrez une notification lors de l'expédition</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Notre livreur vous contactera avant la livraison</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                {{-- Orange Money à la livraison --}}
                <div class="bg-white rounded-xl shadow-2xl p-8 mb-8 border-l-4 border-orange-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-3xl mr-6">
                            📱
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Paiement Orange Money à la livraison</h3>
                            <div class="bg-orange-50 rounded-lg p-4 mb-4">
                                <p class="text-orange-900 font-semibold text-lg mb-2">
                                    📱 Montant à payer : <span class="text-2xl font-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                                </p>
                                <p class="text-orange-800 text-sm">
                                    Préparez votre téléphone Orange Money
                                </p>
                            </div>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Notre livreur vous assistera pour la transaction Orange Money</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Assurez-vous que votre compte est approvisionné</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Vous pouvez aussi payer en espèces si vous préférez</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

        @elseif($order->payment_method === 'moov_money')
            @if($order->payment_status === 'paid')
                {{-- Moov Money payé --}}
                <div class="bg-white rounded-xl shadow-2xl p-8 mb-8 border-l-4 border-green-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-3xl mr-6">
                            ✅
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Paiement Moov Money confirmé</h3>
                            <div class="bg-green-50 rounded-lg p-4 mb-4">
                                <p class="text-green-900 font-semibold text-lg">
                                    ✅ Paiement de <span class="text-xl font-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span> reçu avec succès
                                </p>
                            </div>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Votre commande est en cours de préparation</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Vous recevrez une notification lors de l'expédition</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Notre livreur vous contactera avant la livraison</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                {{-- Moov Money à la livraison --}}
                <div class="bg-white rounded-xl shadow-2xl p-8 mb-8 border-l-4 border-blue-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-3xl mr-6">
                            📱
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Paiement Moov Money à la livraison</h3>
                            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                                <p class="text-blue-900 font-semibold text-lg mb-2">
                                    📱 Montant à payer : <span class="text-2xl font-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                                </p>
                                <p class="text-blue-800 text-sm">
                                    Préparez votre téléphone Moov Money
                                </p>
                            </div>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Notre livreur vous assistera pour la transaction Moov Money</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Assurez-vous que votre compte est approvisionné</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Vous pouvez aussi payer en espèces si vous préférez</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

        @elseif($order->payment_method === 'bank_transfer')
            @if($order->payment_status === 'paid')
                {{-- Virement payé --}}
                <div class="bg-white rounded-xl shadow-2xl p-8 mb-8 border-l-4 border-green-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center text-3xl mr-6">
                            ✅
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Virement bancaire confirmé</h3>
                            <div class="bg-green-50 rounded-lg p-4 mb-4">
                                <p class="text-green-900 font-semibold text-lg">
                                    ✅ Virement de <span class="text-xl font-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span> reçu et vérifié
                                </p>
                            </div>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Votre paiement a été validé par notre service comptabilité</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Votre commande est en cours de préparation</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-green-600 mr-2">✓</span>
                                    <span>Vous recevrez une notification lors de l'expédition</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                {{-- Virement en attente --}}
                <div class="bg-white rounded-xl shadow-2xl p-8 mb-8 border-l-4 border-purple-500">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-3xl mr-6">
                            🏦
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">En attente de virement bancaire</h3>
                            <div class="bg-purple-50 rounded-lg p-4 mb-4">
                                <p class="text-purple-900 font-semibold text-lg mb-2">
                                    🏦 Montant à virer : <span class="text-2xl font-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                                </p>
                                <p class="text-purple-800 text-sm">
                                    Effectuez le virement selon les instructions reçues
                                </p>
                            </div>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-2">→</span>
                                    <span>Effectuez le virement vers le compte <strong>410730007217 (UBA)</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-2">→</span>
                                    <span>Envoyez-nous le reçu par WhatsApp ou email</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-purple-600 mr-2">→</span>
                                    <span>Ou payez à la livraison selon votre préférence</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

{{-- Détails de la commande --}}
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            {{-- Récapitulatif --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">📋</span> Récapitulatif
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Numéro de commande</p>
                        <p class="text-xl font-mono font-bold text-green-600">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Date de commande</p>
                        <p class="text-gray-900 font-semibold">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-1">Montant total</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} <span class="text-lg">FCFA</span></p>
                    </div>
                </div>
            </div>

            {{-- Statut --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="text-2xl mr-2">📊</span> Statut
                </h3>
                <div class="space-y-4">
                    @php
                        $statusText = '';
                        $statusColor = '';
                        $statusIcon = '';

                        if ($order->payment_status === 'paid') {
                            $statusText = 'Confirmée et payée';
                            $statusColor = 'bg-green-100 text-green-800 border-green-300';
                            $statusIcon = '✅';
                        } elseif ($order->payment_method === 'cash_on_delivery') {
                            $statusText = 'Confirmée - Paiement espèces à la livraison';
                            $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                            $statusIcon = '💰';
                        } elseif ($order->payment_method === 'orange_money') {
                            $statusText = 'Confirmée - Orange Money à la livraison';
                            $statusColor = 'bg-orange-100 text-orange-800 border-orange-300';
                            $statusIcon = '📱';
                        } elseif ($order->payment_method === 'moov_money') {
                            $statusText = 'Confirmée - Moov Money à la livraison';
                            $statusColor = 'bg-blue-100 text-blue-800 border-blue-300';
                            $statusIcon = '📱';
                        } elseif ($order->payment_method === 'bank_transfer') {
                            $statusText = 'Confirmée - En attente de virement';
                            $statusColor = 'bg-purple-100 text-purple-800 border-purple-300';
                            $statusIcon = '🏦';
                        }
                    @endphp

                    <div class="border-2 {{ $statusColor }} rounded-lg p-4 text-center">
                        <span class="text-3xl block mb-2">{{ $statusIcon }}</span>
                        <p class="font-bold">{{ $statusText }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Commande reçue</span>
                            <span class="text-green-600 font-bold">✓</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Paiement {{ $order->payment_status === 'paid' ? 'reçu' : 'en attente' }}</span>
                            <span class="{{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }} font-bold">{{ $order->payment_status === 'paid' ? '✓' : '⏳' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Préparation en cours</span>
                            <span class="text-gray-400 font-bold">○</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Livraison</span>
                            <span class="text-gray-400 font-bold">○</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Articles commandés --}}
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="text-2xl mr-2">🛍️</span> Articles commandés ({{ $order->orderItems->count() }})
            </h3>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                <div class="flex items-center justify-between py-4 border-b border-gray-200 last:border-b-0">
                    <div class="flex items-center flex-1">
                        <img src="{{ $item->product->first_image ?? '/images/placeholder.jpg' }}" 
                             alt="{{ $item->product_name }}" 
                             class="w-16 h-16 object-cover rounded-lg border-2 border-gray-200 mr-4">
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $item->product_name }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-gray-900">
                            {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Informations de livraison --}}
        <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <span class="text-2xl mr-2">🚚</span> Informations de livraison
            </h3>
            <div class="bg-gray-50 rounded-lg p-6 space-y-3">
                <div class="flex items-start">
                    <span class="text-gray-600 font-semibold w-32">📍 Adresse :</span>
                    <span class="text-gray-900">{{ $order->delivery_address }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-gray-600 font-semibold w-32">🏙️ Ville :</span>
                    <span class="text-gray-900">{{ $order->delivery_city }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-gray-600 font-semibold w-32">📞 Téléphone :</span>
                    <span class="text-gray-900">{{ $order->delivery_phone }}</span>
                </div>
            </div>
        </div>

        {{-- Boutons d'action --}}
        <div class="text-center mb-8">
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-xl transition-all transform hover:scale-105">
                    <span class="mr-2">🛍️</span> Continuer mes achats
                </a>

                @if($order->payment_status === 'pending' && $order->payment_method === 'bank_transfer')
                <a href="mailto:{{ $email }}?subject=Commande {{ $order->order_number }}&body=Bonjour,%0D%0A%0D%0AJe vous envoie le justificatif de paiement pour ma commande {{ $order->order_number }}.%0D%0A%0D%0AMontant: {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA"
                   class="inline-flex items-center justify-center px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-bold text-lg rounded-lg shadow-md hover:shadow-xl transition-all">
                    <span class="mr-2">📧</span> Envoyer justificatif
                </a>
                @endif
            </div>

            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200">
                <p class="text-gray-700 mb-3 font-semibold">💬 Besoin d'aide ou d'informations ?</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ $telUrl }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-white hover:bg-blue-50 border-2 border-blue-300 text-blue-700 font-bold rounded-lg transition">
                        <span class="mr-2">📞</span> {{ $phone }}
                    </a>
                    <a href="{{ $waUrl }}?text=Bonjour, j'ai une question concernant ma commande {{ $order->order_number }}"
                       target="_blank"
                       class="inline-flex items-center justify-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg shadow-md transition">
                        <span class="mr-2">💬</span> WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- WhatsApp auto-open --}}
@if(session('open_whatsapp') && session('whatsapp_url'))
<div class="fixed bottom-6 right-6 z-50 animate-bounce">
    <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm border-2 border-green-500">
        <div class="text-center">
            <div class="text-5xl mb-3">📱</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">WhatsApp va s'ouvrir</h3>
            <p class="text-sm text-gray-600 mb-4">
                L'application va s'ouvrir avec un message pré-rempli. Cliquez sur "Envoyer" pour nous notifier.
            </p>
            <a href="{{ session('whatsapp_url') }}" 
               target="_blank"
               class="inline-flex items-center justify-center w-full px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-lg transition">
                <span class="mr-2">💬</span> Ouvrir WhatsApp
            </a>
        </div>
    </div>
</div>

<script>
setTimeout(function() {
    window.open('{{ session("whatsapp_url") }}', '_blank');
}, 1500);
</script>
@endif

@push('styles')
<style>
@keyframes bounce {
    0%, 100% {
        transform: translateY(-25%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}

.animate-bounce {
    animation: bounce 1s infinite;
}
</style>
@endpush

@endsection

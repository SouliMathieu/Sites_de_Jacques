@extends('layouts.public')
@section('title', 'Commande confirmée - ' . $order->order_number)
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- En-tête avec message adaptatif -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">
                Merci {{ $order->customer->name }} pour votre commande
            </h1>

            <!-- Message adaptatif selon le mode de paiement -->
            @if($order->payment_method === 'cash')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-center">
                        <div class="text-4xl mr-3">💰</div>
                        <div>
                            <h3 class="font-semibold text-yellow-800">Paiement en espèces à la livraison</h3>
                            <p class="text-sm text-yellow-700">
                                Préparez le montant exact : <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong><br>
                                Notre livreur vous contactera avant la livraison.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($order->payment_method === 'orange_money')
                @if($order->payment_status === 'paid')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <div class="text-4xl mr-3">✅</div>
                            <div>
                                <h3 class="font-semibold text-green-800">Paiement Orange Money confirmé</h3>
                                <p class="text-sm text-green-700">
                                    Votre paiement de {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA a été reçu avec succès.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <div class="text-4xl mr-3">📱</div>
                            <div>
                                <h3 class="font-semibold text-orange-800">Paiement Orange Money à la livraison</h3>
                                <p class="text-sm text-orange-700">
                                    Préparez votre téléphone Orange Money pour payer <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong><br>
                                    Notre livreur vous assistera pour la transaction.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif($order->payment_method === 'moov_money')
                @if($order->payment_status === 'paid')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <div class="text-4xl mr-3">✅</div>
                            <div>
                                <h3 class="font-semibold text-green-800">Paiement Moov Money confirmé</h3>
                                <p class="text-sm text-green-700">
                                    Votre paiement de {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA a été reçu avec succès.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <div class="text-4xl mr-3">📱</div>
                            <div>
                                <h3 class="font-semibold text-blue-800">Paiement Moov Money à la livraison</h3>
                                <p class="text-sm text-blue-700">
                                    Préparez votre téléphone Moov Money pour payer <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong><br>
                                    Notre livreur vous assistera pour la transaction.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @elseif($order->payment_method === 'bank_transfer')
                @if($order->payment_status === 'paid')
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <div class="text-4xl mr-3">✅</div>
                            <div>
                                <h3 class="font-semibold text-green-800">Virement bancaire confirmé</h3>
                                <p class="text-sm text-green-700">
                                    Votre virement de {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA a été reçu et vérifié.
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <div class="text-4xl mr-3">🏦</div>
                            <div>
                                <h3 class="font-semibold text-purple-800">En attente de virement bancaire</h3>
                                <p class="text-sm text-purple-700">
                                    Effectuez le virement de <strong>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong> selon les instructions<br>
                                    ou payez à la livraison selon votre préférence.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Détails de la commande -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Numéro de commande</h3>
                    <p class="text-xl font-mono text-green-600">{{ $order->order_number }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Date</h3>
                    <p class="text-gray-700">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Montant total</h3>
                    <p class="text-xl font-bold text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Statut de la commande</h3>
                    @php
                        $statusText = '';
                        $statusColor = '';
                        $statusIcon = '';

                        if ($order->payment_status === 'paid') {
                            $statusText = 'Confirmée et payée';
                            $statusColor = 'bg-green-100 text-green-800';
                            $statusIcon = '✅';
                        } elseif ($order->payment_method === 'cash') {
                            $statusText = 'Confirmée - Paiement espèces à la livraison';
                            $statusColor = 'bg-yellow-100 text-yellow-800';
                            $statusIcon = '💰';
                        } elseif ($order->payment_method === 'orange_money') {
                            $statusText = 'Confirmée - Orange Money à la livraison';
                            $statusColor = 'bg-orange-100 text-orange-800';
                            $statusIcon = '📱';
                        } elseif ($order->payment_method === 'moov_money') {
                            $statusText = 'Confirmée - Moov Money à la livraison';
                            $statusColor = 'bg-blue-100 text-blue-800';
                            $statusIcon = '📱';
                        } elseif ($order->payment_method === 'bank_transfer') {
                            $statusText = 'Confirmée - En attente de virement';
                            $statusColor = 'bg-purple-100 text-purple-800';
                            $statusIcon = '🏦';
                        }
                    @endphp

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColor }}">
                        {{ $statusIcon }} {{ $statusText }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Articles commandés -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Articles commandés</h3>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ $item->product->name }}</h4>
                        <p class="text-sm text-gray-600">
                            {{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900">
                            {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Informations de livraison -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de livraison</h3>
            <div class="space-y-2">
                <p><strong>Adresse :</strong> {{ $order->delivery_address }}</p>
                <p><strong>Ville :</strong> {{ $order->delivery_city }}</p>
                <p><strong>Téléphone :</strong> {{ $order->delivery_phone }}</p>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="text-center">
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-6">
                <a href="{{ route('home') }}"
                   class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300">
                    Continuer mes achats
                </a>

                @if($order->payment_status === 'pending' && $order->payment_method === 'bank_transfer')
                <a href="mailto:contact@grossiste-ouaga.com?subject=Commande {{ $order->order_number }}"
                   class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition duration-300">
                    Envoyer justificatif de paiement
                </a>
                @endif
            </div>

            <p class="text-gray-600 mb-4">
                Pour toute question, contactez-nous au
                <a href="tel:+22665033700" class="text-green-600 font-semibold hover:text-green-700">
                    +226 65033700
                </a>
                ou via WhatsApp
            </p>
        </div>
    </div>
</div>
@if(session('open_whatsapp') && session('whatsapp_url'))
<script>
    // Ouvrir WhatsApp automatiquement après 1 seconde
    setTimeout(function() {
        window.location.href = '{{ session("whatsapp_url") }}';
    }, 1000);
</script>

<!-- Message informatif pour l'utilisateur -->
<div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
    <div class="text-4xl mb-2">📱</div>
    <h3 class="text-lg font-semibold text-green-800 mb-2">WhatsApp va s'ouvrir automatiquement</h3>
    <p class="text-sm text-green-700 mb-3">
        L'application WhatsApp va s'ouvrir avec un message pré-rempli.
        Il vous suffira de cliquer sur "Envoyer" pour nous notifier.
    </p>
    <p class="text-xs text-green-600">
        Si WhatsApp ne s'ouvre pas automatiquement,
        <a href="{{ session('whatsapp_url') }}" target="_blank" class="underline font-semibold">
            cliquez ici
        </a>
    </p>
</div>
@endif

@endsection

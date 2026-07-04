@extends('layouts.app')

@section('title', 'Paiement - Commande ' . $order->order_number)

@section('content')
@php
    $omPhone  = \App\Models\SiteSetting::get('phone', '+226 77 12 65 19');
    $moovPhone = \App\Models\SiteSetting::get('whatsapp', '22663952032');
    $omNumber  = preg_replace('/\s+|\+/', '', $omPhone);
@endphp

{{-- Hero Header Paiement --}}
<div class="bg-gradient-to-r from-green-600 to-green-700 py-10">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-3xl font-montserrat font-bold text-white mb-2">
                Paiement de votre commande
            </h1>
            <p class="text-green-100">
                Commande {{ $order->order_number }} • Montant total :
                <span class="font-semibold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
            </p>
        </div>
    </div>
</div>

{{-- Progress Steps --}}
<div class="bg-white border-b">
    <div class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-bold">1</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-500">Informations</p>
                        <p class="text-xs text-gray-400">Vos coordonnées</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-green-500 mx-4"></div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-green-600 text-white rounded-full font-bold">2</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-900">Paiement</p>
                        <p class="text-xs text-gray-500">Choix et instructions</p>
                    </div>
                </div>
                <div class="flex-1 h-1 bg-gray-300 mx-4"></div>
                <div class="flex items-center flex-1">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold">3</div>
                    <div class="ml-3">
                        <p class="font-semibold text-gray-500">Confirmation</p>
                        <p class="text-xs text-gray-400">Récapitulatif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Contenu Paiement --}}
<div class="container mx-auto px-4 py-10">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-3 gap-8">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Orange Money --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">OM</div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Orange Money</h2>
                        <p class="text-xs text-gray-500">Paiement mobile sécurisé</p>
                    </div>
                </div>

                <p class="text-sm text-gray-700 mb-4">
                    Payez via code USSD ou choisissez de régler à la livraison.
                </p>

                {{-- USSD + livraison --}}
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-2">
                            <p class="text-xs font-semibold text-orange-800 mb-1">Code USSD pré-rempli :</p>
                            <p class="font-mono text-sm text-orange-900">*144*2*1*{{ $omNumber }}*{{ (int)$order->total_amount }}*PIN#</p>
                            <p class="text-xs text-gray-500 mt-1">Remplacez PIN par votre mot de passe Orange Money</p>
                        </div>
                        <a href="tel:*144*2*1*{{ $omNumber }}*{{ (int)$order->total_amount }}%23"
                           class="inline-flex items-center justify-center w-full px-3 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold">
                            ✳️ Lancer le paiement Orange Money
                        </a>
                    </div>

                    <form method="POST" action="{{ route('orders.payment-at-delivery', $order) }}">
                        @csrf
                        <input type="hidden" name="payment_method" value="orange_money">
                        <input type="hidden" name="payment_at_delivery" value="1">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold border border-gray-300">
                            💰 Payer Orange Money à la livraison
                        </button>
                    </form>
                </div>

                {{-- Confirmation OM --}}
                <form method="POST" action="{{ route('orders.confirm-payment', $order) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="orange_money">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Numéro Orange Money (optionnel)</label>
                            <input type="text" name="payment_phone" value="{{ old('payment_phone', $order->payment_phone) }}"
                                placeholder="+226 XX XX XX XX" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Trans ID / Référence <span class="text-red-500">*</span></label>
                            <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" required
                                placeholder="Ex : 4A5B6C..." class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Le Trans ID se trouve dans votre reçu Orange Money.</p>
                    <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                        ✅ Confirmer le paiement Orange Money
                    </button>
                </form>
            </div>

            {{-- Moov Money --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">MM</div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Moov Money</h2>
                        <p class="text-xs text-gray-500">Paiement mobile sécurisé</p>
                    </div>
                </div>

                <p class="text-sm text-gray-700 mb-4">
                    Payez via code USSD ou choisissez de régler à la livraison.
                </p>

                {{-- USSD + livraison --}}
                <div class="grid md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-2">
                            <p class="text-xs font-semibold text-blue-800 mb-1">Code USSD pré-rempli :</p>
                            <p class="font-mono text-sm text-blue-900">*555*1*{{ $moovPhone }}*{{ (int)$order->total_amount }}*PIN#</p>
                            <p class="text-xs text-gray-500 mt-1">Remplacez PIN par votre mot de passe Moov Money</p>
                        </div>
                        <a href="tel:*555*1*{{ $moovPhone }}*{{ (int)$order->total_amount }}%23"
                           class="inline-flex items-center justify-center w-full px-3 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
                            ✳️ Lancer le paiement Moov Money
                        </a>
                    </div>

                    <form method="POST" action="{{ route('orders.payment-at-delivery', $order) }}">
                        @csrf
                        <input type="hidden" name="payment_method" value="moov_money">
                        <input type="hidden" name="payment_at_delivery" value="1">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold border border-gray-300">
                            💰 Payer Moov Money à la livraison
                        </button>
                    </form>
                </div>

                {{-- Confirmation Moov --}}
                <form method="POST" action="{{ route('orders.confirm-payment', $order) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="moov_money">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Numéro Moov Money (optionnel)</label>
                            <input type="text" name="payment_phone" value="{{ old('payment_phone', $order->payment_phone) }}"
                                placeholder="+226 XX XX XX XX" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Trans ID / Référence <span class="text-red-500">*</span></label>
                            <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" required
                                placeholder="Ex : 7D8E9F..." class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Le Trans ID est visible dans le reçu Moov Money.</p>
                    <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                        ✅ Confirmer le paiement Moov Money
                    </button>
                </form>
            </div>

            {{-- Virement bancaire --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-full flex items-center justify-center text-white text-2xl mr-4 shadow-md">🏦</div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Virement bancaire</h2>
                        <p class="text-xs text-gray-500">Transfert bancaire classique</p>
                    </div>
                </div>

                <p class="text-sm text-gray-700 mb-4">
                    Effectuez un virement depuis votre application bancaire ou en agence.
                </p>

                <div class="text-xs text-gray-600 bg-indigo-50 border border-indigo-100 rounded-lg p-3 mb-4">
                    <p class="font-semibold mb-1">Informations bancaires :</p>
                    <p>Banque : XXX</p>
                    <p>RIB / IBAN : 1234 5678 9012</p>
                    <p>Intitulé : Jackson Energy International</p>
                </div>

                <p class="text-xs text-gray-500 mb-4">Après le virement, saisissez la référence ci-dessous pour confirmer.</p>

                <form method="POST" action="{{ route('orders.confirm-payment', $order) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="bank_transfer">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Référence du virement <span class="text-red-500">*</span></label>
                            <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" required
                                placeholder="Numéro de transaction / libellé" class="w-full px-3 py-2 border rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Numéro de téléphone (optionnel)</label>
                            <input type="text" name="payment_phone" value="{{ old('payment_phone', $order->payment_phone) }}"
                                class="w-full px-3 py-2 border rounded-lg text-sm" placeholder="+226 XX XX XX XX">
                        </div>
                    </div>
                    <button type="submit" class="mt-2 inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                        ✅ Confirmer le virement bancaire
                    </button>
                </form>
            </div>

            {{-- Paiement à la livraison (espèces) --}}
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-gray-500">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-500 to-gray-700 rounded-full flex items-center justify-center text-white text-2xl mr-4 shadow-md">💵</div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Paiement à la livraison</h2>
                        <p class="text-xs text-gray-500">Espèces</p>
                    </div>
                </div>

                <p class="text-sm text-gray-700 mb-4">
                    Réglez en espèces directement à la livraison.
                </p>

                <form method="POST" action="{{ route('orders.payment-at-delivery', $order) }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="payment_method" value="cash_on_delivery">
                    <input type="hidden" name="payment_at_delivery" value="1">
                    <p class="text-xs text-gray-600">
                        Montant à préparer : <span class="font-semibold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                    </p>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-lg">
                        ✅ Confirmer le paiement à la livraison
                    </button>
                </form>
            </div>
        </div>

        {{-- Récapitulatif --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 border-t-4 border-green-500">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">🧾</span> Récapitulatif
                </h3>

                <div class="space-y-3 mb-4">
                    <div class="text-sm text-gray-700">
                        <p class="font-semibold mb-1">Client</p>
                        <p>{{ $order->customer_name }}</p>
                        <p>📞 {{ $order->customer_phone }}</p>
                        @if($order->customer_email)<p>✉️ {{ $order->customer_email }}</p>@endif
                        @if($order->customer_company)<p>🏢 {{ $order->customer_company }}</p>@endif
                    </div>
                    <div class="text-sm text-gray-700">
                        <p class="font-semibold mb-1">Livraison</p>
                        <p>{{ $order->delivery_address }}</p>
                        <p>{{ $order->delivery_city }}</p>
                        <p>📞 {{ $order->delivery_phone }}</p>
                    </div>
                </div>

                <div class="border-t pt-4 mt-2 space-y-3">
                    @foreach($order->orderItems as $item)
                        <div class="flex justify-between text-sm">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 border-t pt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sous-total</span>
                        <span class="font-semibold text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Frais de livraison</span>
                        <span class="font-semibold text-green-600">Gratuit</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t">
                        <span class="font-bold text-gray-900">Total à payer</span>
                        <span class="font-bold text-green-600 text-lg">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                <div class="mt-6 text-xs text-gray-500">
                    <p>Après le paiement, vous serez redirigé vers une page de confirmation.</p>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center text-xs text-gray-500 hover:text-gray-700">
                    ← Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.public')

@section('title', 'Paiement - Commande ' . $order->order_number)

@section('content')
<div class="bg-gray-100 py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-montserrat font-bold text-gris-moderne mb-4">
            Finaliser le paiement
        </h1>
        <p class="text-gray-600">Commande {{ $order->order_number }}</p>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Résumé de la commande -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-montserrat font-bold mb-6 text-gris-moderne">
                    📋 Résumé de la commande
                </h2>

                <!-- Informations client -->
                <div class="border-b pb-4 mb-4">
                    <h3 class="font-semibold mb-2">👤 Client</h3>
                    <p class="text-gray-700">{{ $order->customer->name }}</p>
                    <p class="text-gray-600">{{ $order->customer->phone }}</p>
                    @if($order->customer->email)
                    <p class="text-gray-600">{{ $order->customer->email }}</p>
                    @endif
                    @if($order->customer->company)
                    <p class="text-gray-600">{{ $order->customer->company }}</p>
                    @endif
                </div>

                <!-- Produits commandés -->
                <div class="border-b pb-4 mb-4">
                    <h3 class="font-semibold mb-3">🛍️ Produits commandés</h3>
                    @foreach($order->orderItems as $item)
                    <div class="flex items-center space-x-3 mb-3">
                        <img src="{{ $item->product->first_image }}" alt="{{ $item->product->name }}" class="w-12 h-12 object-cover rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-600">{{ $item->quantity }} × {{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <p class="font-semibold">{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endforeach
                </div>

                <!-- Livraison -->
                <div class="border-b pb-4 mb-4">
                    <h3 class="font-semibold mb-2">🚚 Livraison</h3>
                    <p class="text-gray-700">{{ $order->delivery_address }}</p>
                    <p class="text-gray-600">{{ $order->delivery_city }}</p>
                    <p class="text-gray-600">{{ $order->delivery_phone }}</p>
                </div>

                <!-- Total -->
                <div class="text-right">
                    <p class="text-2xl font-bold text-vert-energie">
                        Total: {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>

            <!-- Instructions de paiement -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-montserrat font-bold mb-6 text-gris-moderne">
                    💳 Instructions de paiement
                </h2>

                @if($order->payment_method == 'orange_money')
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">O</div>
                        <h3 class="text-lg font-semibold">Orange Money</h3>
                    </div>

                    <div class="space-y-4">
                        <p class="font-medium">Choisissez votre méthode de paiement :</p>

                        <!-- Option 1: MaxIt App -->
                        <div class="bg-white border border-orange-300 rounded-lg p-4">
                            <h4 class="font-semibold text-orange-700 mb-2">📱 Via l'application MaxIt (Recommandé)</h4>
                            <p class="text-sm text-gray-600 mb-3">Si vous avez l'application MaxIt installée sur votre téléphone</p>

                            <button onclick="openMaxItApp()"
                                    class="w-full bg-orange-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-600 transition flex items-center justify-center mb-2">
                                📱 Ouvrir MaxIt
                            </button>

                            <div class="bg-orange-100 p-3 rounded text-xs text-orange-800">
                                <strong>Instructions :</strong><br>
                                1. L'application MaxIt s'ouvrira automatiquement<br>
                                2. Allez dans "Transfert" → "Envoi d'argent"<br>
                                3. Numéro : 65033700<br>
                                4. Montant : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA<br>
                                5. Confirmez avec votre PIN
                            </div>
                        </div>

                        <!-- Option 2: Code USSD -->
                        <div class="bg-white border border-orange-300 rounded-lg p-4">
                            <h4 class="font-semibold text-orange-700 mb-2">📞 Via code USSD (Alternative)</h4>
                            <p class="text-sm text-gray-600 mb-3">Si vous n'avez pas MaxIt ou préférez le code classique</p>

                            <button onclick="openOrangeUSSD()"
                                    class="w-full bg-orange-400 text-white py-3 px-4 rounded-lg font-semibold hover:bg-orange-500 transition flex items-center justify-center mb-2">
                                📞 Composer le code
                            </button>

                            <div class="bg-orange-100 p-3 rounded text-xs text-orange-800">
                                <strong>Code à composer :</strong><br>
                                <code class="bg-white px-2 py-1 rounded">*144*2*1*65033700*{{ number_format($order->total_amount, 0, '', '') }}#</code><br>
                                Ou composez *144# et suivez les instructions
                            </div>
                        </div>
                        <!-- AJOUT : Paiement à la livraison pour Orange Money -->
        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
      <h5 class="font-semibold text-blue-800 mb-2 flex items-center">
        <span class="text-2xl mr-2">🚚</span>
        Ou payez Orange Money à la livraison
       </h5>
          <p class="text-sm text-blue-700 mb-3">
          Vous pouvez choisir de payer directement à la livraison avec Orange Money.
            Notre livreur vous assistera pour la transaction.
             </p>

                <form action="{{ route('orders.payment-at-delivery', $order) }}" method="POST" class="inline">
              @csrf
             <input type="hidden" name="payment_at_delivery" value="1">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                Payer Orange Money à la livraison
                   </button>
                  </form>
                  </div>

                    </div>
                </div>

                @elseif($order->payment_method == 'moov_money')
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">M</div>
                        <h3 class="text-lg font-semibold">Moov Money</h3>
                    </div>

                    <div class="space-y-4">
                        <p class="font-medium">Choisissez votre méthode de paiement :</p>

                        <!-- Option 1: Moov Money App -->
                        <div class="bg-white border border-blue-300 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-700 mb-2">📱 Via l'application Moov Money (Recommandé)</h4>
                            <p class="text-sm text-gray-600 mb-3">Si vous avez l'application Moov Money installée</p>

                            <button onclick="openMoovApp()"
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 transition flex items-center justify-center mb-2">
                                📱 Ouvrir Moov Money
                            </button>

                            <div class="bg-blue-100 p-3 rounded text-xs text-blue-800">
                                <strong>Instructions :</strong><br>
                                1. L'application Moov Money s'ouvrira<br>
                                2. Allez dans "Transfert" → "Envoi d'argent"<br>
                                3. Numéro : 70103993<br>
                                4. Montant : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA<br>
                                5. Confirmez avec votre PIN
                            </div>
                        </div>

                        <!-- Option 2: Code USSD -->
                        <div class="bg-white border border-blue-300 rounded-lg p-4">
                            <h4 class="font-semibold text-blue-700 mb-2">📞 Via code USSD (Alternative)</h4>
                            <p class="text-sm text-gray-600 mb-3">Si vous n'avez pas l'app ou préférez le code classique</p>

                            <button onclick="openMoovUSSD()"
                                    class="w-full bg-blue-500 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-600 transition flex items-center justify-center mb-2">
                                📞 Composer le code
                            </button>

                            <div class="bg-blue-100 p-3 rounded text-xs text-blue-800">
                                <strong>Code à composer :</strong><br>
                                <code class="bg-white px-2 py-1 rounded">*555*2*1*70103993*{{ number_format($order->total_amount, 0, '', '') }}#</code><br>
                                Ou composez *555# et suivez les instructions
                            </div>
                            <!-- AJOUT : Paiement à la livraison pour Moov Money -->
<div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
    <h5 class="font-semibold text-green-800 mb-2 flex items-center">
        <span class="text-2xl mr-2">🚚</span>
        Ou payez Moov Money à la livraison
    </h5>
    <p class="text-sm text-green-700 mb-3">
        Vous pouvez choisir de payer directement à la livraison avec Moov Money.
        Notre livreur vous assistera pour la transaction.
    </p>

    <form action="{{ route('orders.payment-at-delivery', $order) }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="payment_at_delivery" value="1">
        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-300">
            Payer Moov Money à la livraison
        </button>
    </form>
</div>

                        </div>
                    </div>
                </div>

                @elseif($order->payment_method == 'bank_transfer')
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">🏦</div>
                        <h3 class="text-lg font-semibold">Virement bancaire</h3>
                    </div>

                    <div class="space-y-4">
                        <p class="font-medium">Choisissez votre méthode de virement :</p>

                        <!-- Option 1: UBA Mobile App -->
                        <div class="bg-white border border-green-300 rounded-lg p-4">
                            <h4 class="font-semibold text-green-700 mb-2">📱 Via l'application UBA Mobile (Recommandé)</h4>
                            <p class="text-sm text-gray-600 mb-3">Si vous avez l'application UBA Mobile installée</p>

                            <button onclick="openUBAApp()"
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-green-700 transition flex items-center justify-center mb-2">
                                📱 Ouvrir UBA Mobile
                            </button>

                            <div class="bg-green-100 p-3 rounded text-xs text-green-800">
                                <strong>Instructions :</strong><br>
                                1. L'application UBA Mobile s'ouvrira<br>
                                2. Allez dans "Transfert" → "Vers un autre compte UBA"<br>
                                3. Compte : 410730007217<br>
                                4. Bénéficiaire : Mr ZIDA ABDOULAYE<br>
                                5. Montant : {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA<br>
                                6. Référence : {{ $order->order_number }}
                            </div>
                        </div>

                        <!-- Option 2: Coordonnées bancaires -->
                        <div class="bg-white border border-green-300 rounded-lg p-4">
                            <h4 class="font-semibold text-green-700 mb-2">🏦 Coordonnées bancaires (Alternative)</h4>
                            <p class="text-sm text-gray-600 mb-3">Pour virement depuis une autre banque ou agence</p>

                            <div class="bg-green-100 p-4 rounded-lg space-y-2 text-sm">
                                <p><strong>Banque :</strong> UBA Burkina Faso</p>
                                <p><strong>Titulaire :</strong> Mr ZIDA ABDOULAYE</p>
                                <p><strong>Numéro de compte :</strong> 410730007217</p>
                                <p><strong>Montant :</strong> {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</p>
                                <p><strong>Référence :</strong> {{ $order->order_number }}</p>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Après le virement, envoyez-nous le reçu par WhatsApp ou email.</p>
                        </div>
                        <!-- AJOUT : Paiement à la livraison pour virement -->
<div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
    <h5 class="font-semibold text-yellow-800 mb-2 flex items-center">
        <span class="text-2xl mr-2">🚚</span>
        Ou réglez à la livraison
    </h5>
    <p class="text-sm text-yellow-700 mb-3">
        Si vous préférez, vous pouvez régler à la livraison par virement bancaire,
        Mobile Money ou espèces selon votre convenance.
    </p>

    <form action="{{ route('orders.payment-at-delivery', $order) }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="payment_at_delivery" value="1">
        <button type="submit" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition duration-300">
            Payer à la livraison
        </button>
    </form>
</div>

                    </div>
                </div>

                @elseif($order->payment_method == 'cash')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">💵</div>
                        <h3 class="text-lg font-semibold">Paiement à la livraison</h3>
                    </div>

                    <div class="space-y-3">
                        <p class="font-medium">Informations importantes :</p>
                        <ul class="list-disc list-inside space-y-2 text-sm">
                            <li>Préparez le montant exact : <strong class="bg-gray-100 px-2 py-1 rounded">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong></li>
                            <li>Le paiement se fera en espèces lors de la livraison</li>
                            <li>Assurez-vous d'être disponible à l'adresse indiquée</li>
                            <li>Notre livreur vous contactera avant la livraison</li>
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Formulaire de confirmation -->
                <form method="POST" action="{{ route('orders.confirm-payment', $order) }}" class="mt-6">
                    @csrf

                    @if(in_array($order->payment_method, ['orange_money', 'moov_money', 'bank_transfer']))
                    <div class="mb-4">
                        <label for="payment_reference" class="block text-sm font-medium text-gray-700 mb-2">
                            @if($order->payment_method == 'bank_transfer')
                            Numéro de référence du virement *
                            @else
                            Numéro de transaction reçu par SMS *
                            @endif
                        </label>
                        <input type="text" id="payment_reference" name="payment_reference" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-vert-energie" placeholder="Ex: MP240625.1234.A12345">
                        <p class="text-sm text-gray-500 mt-1">Ce numéro nous permet de vérifier votre paiement</p>
                    </div>
                    @else
                    <input type="hidden" name="payment_reference" value="CASH_{{ $order->order_number }}">
                    @endif

                    <button type="submit" class="w-full bg-vert-energie text-white py-4 px-6 rounded-lg hover:bg-green-700 transition font-semibold text-lg">
                        ✅ Confirmer le paiement
                    </button>
                </form>

                <!-- Contact d'urgence -->
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm font-medium text-yellow-800 mb-2">💡 Besoin d'aide ?</p>
                    <div class="flex space-x-4">
                        <a href="tel:+22665033700" class="text-yellow-700 hover:text-yellow-900 text-sm">📞 +226 65 03 37 00</a>
                        <a href="https://wa.me/22665033700?text=Bonjour, j'ai besoin d'aide pour ma commande {{ $order->order_number }}" class="text-yellow-700 hover:text-yellow-900 text-sm">💬 WhatsApp</a>
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
        // Essayer d'ouvrir MaxIt avec deep link
        const maxitUrl = 'maxit://transfer?number=65033700&amount={{ number_format($order->total_amount, 0, "", "") }}';
        const fallbackUrl = 'https://play.google.com/store/apps/details?id=com.orange.maxit.bf';

        // Créer un iframe caché pour tester l'ouverture de l'app
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = maxitUrl;
        document.body.appendChild(iframe);

        // Si l'app ne s'ouvre pas après 2 secondes, rediriger vers le Play Store
        setTimeout(() => {
            document.body.removeChild(iframe);
            if (confirm('MaxIt ne semble pas installé. Voulez-vous le télécharger ?')) {
                window.open(fallbackUrl, '_blank');
            }
        }, 2000);

        // Afficher les instructions
        alert('Si MaxIt s\'ouvre :\n1. Allez dans Transfert → Envoi d\'argent\n2. Numéro: 65033700\n3. Montant: {{ number_format($order->total_amount, 0, ",", " ") }} FCFA');
    } else {
        alert('Cette fonctionnalité fonctionne sur mobile. Utilisez le code USSD.');
    }
}

// Fonction pour ouvrir l'app Moov Money
function openMoovApp() {
    if (isMobileDevice()) {
        // Essayer d'ouvrir Moov Money avec deep link
        const moovUrl = 'moovmoney://transfer?number=70103993&amount={{ number_format($order->total_amount, 0, "", "") }}';
        const fallbackUrl = 'https://play.google.com/store/apps/details?id=com.tlc.onatel.customer';

        // Créer un iframe caché pour tester l'ouverture de l'app
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = moovUrl;
        document.body.appendChild(iframe);

        // Si l'app ne s'ouvre pas après 2 secondes, rediriger vers le Play Store
        setTimeout(() => {
            document.body.removeChild(iframe);
            if (confirm('Moov Money ne semble pas installé. Voulez-vous le télécharger ?')) {
                window.open(fallbackUrl, '_blank');
            }
        }, 2000);

        // Afficher les instructions
        alert('Si Moov Money s\'ouvre :\n1. Allez dans Transfert → Envoi d\'argent\n2. Numéro: 70103993\n3. Montant: {{ number_format($order->total_amount, 0, ",", " ") }} FCFA');
    } else {
        alert('Cette fonctionnalité fonctionne sur mobile. Utilisez le code USSD.');
    }
}

// Fonction pour ouvrir UBA Mobile App
function openUBAApp() {
    if (isMobileDevice()) {
        // Essayer d'ouvrir UBA Mobile avec deep link
        const ubaUrl = 'uba://transfer?account=410730007217&amount={{ number_format($order->total_amount, 0, "", "") }}&reference={{ $order->order_number }}';
        const fallbackUrl = 'https://play.google.com/store/apps/details?id=com.uba.mobile';

        // Créer un iframe caché pour tester l'ouverture de l'app
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = ubaUrl;
        document.body.appendChild(iframe);

        // Si l'app ne s'ouvre pas après 2 secondes, rediriger vers le Play Store
        setTimeout(() => {
            document.body.removeChild(iframe);
            if (confirm('UBA Mobile ne semble pas installé. Voulez-vous le télécharger ?')) {
                window.open(fallbackUrl, '_blank');
            }
        }, 2000);

        // Afficher les instructions
        alert('Si UBA Mobile s\'ouvre :\n1. Allez dans Transfert → Vers compte UBA\n2. Compte: 410730007217\n3. Bénéficiaire: Mr ZIDA ABDOULAYE\n4. Montant: {{ number_format($order->total_amount, 0, ",", " ") }} FCFA\n5. Référence: {{ $order->order_number }}');
    } else {
        alert('Cette fonctionnalité fonctionne sur mobile. Utilisez les coordonnées bancaires.');
    }
}

// Fonction pour ouvrir le code USSD Orange (CORRIGÉ)
function openOrangeUSSD() {
    const ussdCode = '*144*2*1*65033700*{{ number_format($order->total_amount, 0, "", "") }}#';

    if (isMobileDevice()) {
        window.location.href = `tel:${ussdCode}`;
    } else {
        alert(`Composez ce code sur votre téléphone Orange :\n${ussdCode}`);

        // Copier dans le presse-papiers si possible
        if (navigator.clipboard) {
            navigator.clipboard.writeText(ussdCode).then(() => {
                alert('Code copié dans le presse-papiers !');
            });
        }
    }
}

// Fonction pour ouvrir le code USSD Moov (CORRIGÉ)
function openMoovUSSD() {
    const ussdCode = '*555*2*1*70103993*{{ number_format($order->total_amount, 0, "", "") }}#';

    if (isMobileDevice()) {
        window.location.href = `tel:${ussdCode}`;
    } else {
        alert(`Composez ce code sur votre téléphone Moov :\n${ussdCode}`);

        // Copier dans le presse-papiers si possible
        if (navigator.clipboard) {
            navigator.clipboard.writeText(ussdCode).then(() => {
                alert('Code copié dans le presse-papiers !');
            });
        }
    }
}
</script>
@endpush
@endsection

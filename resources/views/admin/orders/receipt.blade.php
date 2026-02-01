<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu - Commande {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #008000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #008000;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 11px;
            color: #666;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #EF2B2D;
            text-transform: uppercase;
        }
        .order-info {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .order-info-left, .order-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-section {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: bold;
            color: #008000;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #008000;
            color: white;
            font-weight: bold;
        }
        .table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            margin-top: 20px;
            text-align: right;
        }
        .total-line {
            margin-bottom: 5px;
        }
        .total-final {
            font-size: 16px;
            font-weight: bold;
            color: #EF2B2D;
            border-top: 2px solid #333;
            padding-top: 5px;
        }
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .signature-separator {
            display: table-cell;
            width: 10%;
        }
        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #008000;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 50px;
            margin-bottom: 10px;
        }
        .signature-date {
            font-size: 10px;
            color: #666;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .burkina-colors {
            background: linear-gradient(90deg, #EF2B2D 33%, #FFD100 33%, #FFD100 66%, #008000 66%);
            height: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="burkina-colors"></div>
        <div class="company-info">
            {{ $company['address'] }}<br>
            Tél: {{ $company['phone'] }} | Email: {{ $company['email'] }}
        </div>
    </div>

    <!-- Titre du reçu -->
    <div class="receipt-title">Reçu de Commande</div>

    <!-- Informations commande -->
    <div class="order-info">
        <div class="order-info-left">
            <div class="info-section">
                <span class="info-label">N° Commande:</span> {{ $order->order_number }}
            </div>
            <div class="info-section">
                <span class="info-label">Date:</span> {{ $order->created_at->format('d/m/Y à H:i') }}
            </div>
            <div class="info-section">
                <span class="info-label">Statut:</span> 
                @switch($order->status)
                    @case('pending') En attente @break
                    @case('confirmed') Confirmée @break
                    @case('processing') En préparation @break
                    @case('shipped') Expédiée @break
                    @case('delivered') Livrée @break
                    @case('cancelled') Annulée @break
                @endswitch
            </div>
        </div>
        <div class="order-info-right">
            <div class="info-section">
                <span class="info-label">Client:</span> {{ $order->customer_name }}
            </div>
            <div class="info-section">
                <span class="info-label">Téléphone:</span> {{ $order->customer_phone }}
            </div>
            <div class="info-section">
                <span class="info-label">Email:</span> {{ $order->customer_email }}
            </div>
        </div>
    </div>

    <!-- Tableau des produits -->
    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($item->total_price, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total -->
    <div class="total-section">
        <div class="total-line">
            <strong>Sous-total: {{ number_format($order->orderItems->sum('total_price'), 0, ',', ' ') }} FCFA</strong>
        </div>
        <div class="total-final">
            <strong>TOTAL: {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
        </div>
    </div>

    <!-- Mode de paiement -->
    <div class="info-section" style="margin-top: 20px;">
        <span class="info-label">Mode de paiement:</span> 
        @switch($order->payment_method)
            @case('mobile_money') Mobile Money @break
            @case('bank_transfer') Virement bancaire @break
            @case('cash_on_delivery') Paiement à la livraison @break
            @default {{ ucfirst($order->payment_method) }}
        @endswitch
    </div>

    @if($order->delivery_address)
    <div class="info-section">
        <span class="info-label">Adresse de livraison:</span><br>
        {{ $order->delivery_address }}<br>
        {{ $order->delivery_city }}
    </div>
    @endif

    <!-- Section signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Signature du Vendeur</div>
            <div class="signature-line"></div>
            <div class="signature-date">
                Date: ________________<br>
                Nom: {{ auth()->user()->name ?? 'Jackson Energy' }}
            </div>
        </div>
        <div class="signature-separator"></div>
        <div class="signature-box">
            <div class="signature-title">Signature du Client</div>
            <div class="signature-line"></div>
            <div class="signature-date">
                Date: ________________<br>
                Nom: {{ $order->customer_name }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="burkina-colors"></div>
        <strong>{{ $company['name'] }}</strong> - Solutions énergétiques au Burkina Faso<br>
        Ce document fait foi de la transaction effectuée.<br>
        Merci pour votre confiance !
    </div>
</body>
</html>

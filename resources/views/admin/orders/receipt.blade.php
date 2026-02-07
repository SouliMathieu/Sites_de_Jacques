<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu - Commande {{ $order->order_number }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #2c3e50;
            margin: 0;
            padding: 0;
        }
        
        /* Header avec logo */
        .header {
            text-align: center;
            border-bottom: 4px solid #16a34a;
            padding-bottom: 20px;
            margin-bottom: 25px;
            position: relative;
        }
        
        .company-name {
            font-size: 26px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .company-tagline {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 12px;
            font-style: italic;
        }
        
        .burkina-colors {
            background: linear-gradient(90deg, #EF2B2D 33%, #FFD100 33%, #FFD100 66%, #16a34a 66%);
            height: 8px;
            margin: 12px auto;
            max-width: 300px;
            border-radius: 2px;
        }
        
        .company-info {
            font-size: 10px;
            color: #4b5563;
            line-height: 1.7;
        }
        
        /* Badge titre */
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            padding: 12px 20px;
            background: linear-gradient(135deg, #dc2626 0%, #EF2B2D 100%);
            color: white;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 5px;
        }
        
        /* Section informations */
        .order-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border: 2px solid #16a34a;
            border-radius: 8px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        
        .order-info-left, .order-info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 15px;
        }
        
        .info-card-title {
            font-size: 13px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #16a34a;
            text-transform: uppercase;
        }
        
        .info-section {
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .info-label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            min-width: 90px;
        }
        
        .info-value {
            color: #1f2937;
        }
        
        /* Badge de statut */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        
        /* Section titre */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin: 20px 0 12px 0;
            padding-left: 12px;
            border-left: 5px solid #16a34a;
        }
        
        /* Tableau produits */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .table th {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            font-weight: bold;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table td {
            border: 1px solid #e5e7eb;
            padding: 10px 8px;
            font-size: 10px;
        }
        
        .table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .table tr:nth-child(odd) {
            background-color: white;
        }
        
        .product-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .quantity-badge {
            background: #dcfce7;
            color: #166534;
            padding: 3px 8px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 10px;
        }
        
        /* Section totaux */
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border: 2px solid #fdba74;
            border-radius: 5px;
        }
        
        .total-line {
            margin-bottom: 8px;
            font-size: 11px;
            text-align: right;
            padding: 5px 0;
        }
        
        .total-final {
            font-size: 18px;
            font-weight: bold;
            color: #dc2626;
            border-top: 3px solid #16a34a;
            padding-top: 10px;
            margin-top: 8px;
            text-align: right;
        }
        
        /* Informations paiement et livraison */
        .payment-section {
            margin-top: 20px;
            padding: 12px;
            background: white;
            border-left: 4px solid #16a34a;
            border-radius: 4px;
        }
        
        .payment-row {
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        /* Section signatures */
        .signature-section {
            margin-top: 50px;
            display: table;
            width: 100%;
            border-top: 2px dashed #d1d5db;
            padding-top: 25px;
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
            font-size: 11px;
            color: #16a34a;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        
        .signature-line {
            border-bottom: 2px solid #374151;
            height: 60px;
            margin: 0 auto 12px;
            width: 90%;
        }
        
        .signature-date {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.6;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
            border-top: 3px solid #16a34a;
            padding-top: 15px;
            line-height: 1.8;
        }
        
        .footer-company {
            font-size: 13px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 5px;
        }
        
        .footer-thanks {
            font-size: 12px;
            color: #dc2626;
            font-weight: bold;
            margin-top: 10px;
            font-style: italic;
        }
        
        /* Watermark léger */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(22, 163, 74, 0.04);
            font-weight: bold;
            z-index: -1;
        }
        
        /* Numérotation */
        .page-number {
            position: fixed;
            bottom: 10mm;
            right: 10mm;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="watermark">JACKSON ENERGY</div>
    
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $company['name'] ?? 'Jackson Energy International' }}</div>
        <div class="company-tagline">Solutions énergétiques durables et innovantes</div>
        <div class="burkina-colors"></div>
        <div class="company-info">
            <strong>Adresse:</strong> {{ $company['address'] ?? 'Ouagadougou, Burkina Faso' }}<br>
            <strong>Téléphone:</strong> {{ $company['phone'] ?? '+226 XX XX XX XX' }} | 
            <strong>Email:</strong> {{ $company['email'] ?? 'contact@jackson-energy.bf' }}<br>
            <strong>NIF:</strong> {{ $company['nif'] ?? 'XXXXXXXXXX' }} | 
            <strong>RCCM:</strong> {{ $company['rccm'] ?? 'BF-XXX-XXX' }}
        </div>
    </div>

    <!-- Titre du reçu -->
    <div class="receipt-title">Reçu de Commande Officiel</div>

    <!-- Informations commande et client -->
    <div class="order-info">
        <div class="order-info-left">
            <div class="info-card-title">Détails Commande</div>
            <div class="info-section">
                <span class="info-label">N° Commande:</span>
                <span class="info-value"><strong>{{ $order->order_number }}</strong></span>
            </div>
            <div class="info-section">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
            </div>
            <div class="info-section">
                <span class="info-label">Statut:</span>
                <span class="status-badge status-{{ $order->status }}">
                    @switch($order->status)
                        @case('pending') En attente @break
                        @case('confirmed') Confirmée @break
                        @case('processing') En traitement @break
                        @case('shipped') Expédiée @break
                        @case('delivered') Livrée @break
                        @case('completed') Complétée @break
                        @case('cancelled') Annulée @break
                        @default {{ ucfirst($order->status) }}
                    @endswitch
                </span>
            </div>
        </div>
        <div class="order-info-right">
            <div class="info-card-title">Informations Client</div>
            <div class="info-section">
                <span class="info-label">Nom complet:</span>
                <span class="info-value"><strong>{{ $order->customer_name }}</strong></span>
            </div>
            <div class="info-section">
                <span class="info-label">Téléphone:</span>
                <span class="info-value">{{ $order->customer_phone }}</span>
            </div>
            <div class="info-section">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $order->customer_email }}</span>
            </div>
        </div>
    </div>

    <!-- Produits commandés -->
    <div class="section-title">Produits Commandés</div>
    
    <table class="table">
        <thead>
            <tr>
                <th style="width: 50%;">Produit</th>
                <th class="text-center" style="width: 12%;">Qté</th>
                <th class="text-right" style="width: 19%;">Prix Unit.</th>
                <th class="text-right" style="width: 19%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
            <tr>
                <td class="product-name">{{ $item->product_name }}</td>
                <td class="text-center">
                    <span class="quantity-badge">{{ $item->quantity }}×</span>
                </td>
                <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                <td class="text-right" style="font-weight: bold; color: #16a34a;">
                    {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Section totaux -->
    <div class="total-section">
        <div class="total-line">
            <strong>Sous-total:</strong> 
            {{ number_format($order->orderItems->sum('total_price'), 0, ',', ' ') }} FCFA
        </div>
        @if(($order->discount ?? 0) > 0)
        <div class="total-line" style="color: #dc2626;">
            <strong>Remise:</strong> 
            - {{ number_format($order->discount, 0, ',', ' ') }} FCFA
        </div>
        @endif
        @if(($order->shipping_cost ?? 0) > 0)
        <div class="total-line">
            <strong>Frais de livraison:</strong> 
            {{ number_format($order->shipping_cost, 0, ',', ' ') }} FCFA
        </div>
        @endif
        <div class="total-final">
            MONTANT TOTAL: {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA
        </div>
    </div>

    <!-- Informations paiement et livraison -->
    <div class="payment-section">
        <div class="section-title" style="margin-top: 0; border-left: none; padding-left: 0;">
            Informations Complémentaires
        </div>
        
        <div class="payment-row">
            <span class="info-label">Mode de paiement:</span>
            <span class="info-value">
                @switch($order->payment_method)
                    @case('mobile_money') Mobile Money @break
                    @case('bank_transfer') Virement bancaire @break
                    @case('cash_on_delivery') Paiement à la livraison @break
                    @case('card') Carte bancaire @break
                    @default {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                @endswitch
            </span>
        </div>
        
        @if($order->payment_status)
        <div class="payment-row">
            <span class="info-label">Statut paiement:</span>
            <span class="status-badge status-{{ $order->payment_status }}">
                @switch($order->payment_status)
                    @case('pending') En attente @break
                    @case('paid') Payé @break
                    @case('failed') Échoué @break
                    @default {{ ucfirst($order->payment_status) }}
                @endswitch
            </span>
        </div>
        @endif
        
        @if($order->delivery_address)
        <div class="payment-row" style="margin-top: 12px;">
            <span class="info-label">Adresse de livraison:</span><br>
            <span class="info-value" style="margin-left: 95px;">
                {{ $order->delivery_address }}<br>
                {{ $order->delivery_city ?? '' }}
            </span>
        </div>
        @endif
        
        @if($order->notes)
        <div class="payment-row" style="margin-top: 12px;">
            <span class="info-label">Notes:</span><br>
            <span class="info-value" style="margin-left: 95px; font-style: italic; color: #6b7280;">
                {{ $order->notes }}
            </span>
        </div>
        @endif
    </div>

    <!-- Section signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Signature du Vendeur</div>
            <div class="signature-line"></div>
            <div class="signature-date">
                Nom: {{ auth()->user()->name ?? 'Jackson Energy' }}<br>
                Date: _______________________<br>
                Cachet de l'entreprise
            </div>
        </div>
        <div class="signature-separator"></div>
        <div class="signature-box">
            <div class="signature-title">Signature du Client</div>
            <div class="signature-line"></div>
            <div class="signature-date">
                Nom: {{ $order->customer_name }}<br>
                Date: _______________________<br>
                Reçu en bon état
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="burkina-colors"></div>
        <div class="footer-company">{{ $company['name'] ?? 'Jackson Energy International' }}</div>
        Solutions énergétiques durables au Burkina Faso<br>
        Ce document fait foi de la transaction effectuée et doit être conservé.<br>
        Pour toute réclamation, contactez-nous dans un délai de 48 heures.<br>
        <div class="footer-thanks">Merci pour votre confiance !</div>
    </div>
    
    <div class="page-number">
        Document généré le {{ now()->format('d/m/Y à H:i') }} | Page 1/1
    </div>
</body>
</html>

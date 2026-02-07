<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu - Commande {{ $order->order_number }} - Jackson Energy International</title>
    <style>
        @media print {
            body { 
                margin: 0; 
                padding: 15mm;
            }
            .no-print { 
                display: none !important; 
            }
            .page-break {
                page-break-after: always;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #2c3e50;
            margin: 20px;
            background: #f8f9fa;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        
        /* Header avec logo et informations entreprise */
        .header {
            text-align: center;
            border-bottom: 4px solid #16a34a;
            padding-bottom: 25px;
            margin-bottom: 35px;
            position: relative;
        }
        
        .company-logo {
            font-size: 36px;
            font-weight: 900;
            color: #16a34a;
            margin-bottom: 8px;
            letter-spacing: -1px;
            text-transform: uppercase;
        }
        
        .company-tagline {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 15px;
            font-style: italic;
        }
        
        .burkina-flag {
            background: linear-gradient(90deg, #EF2B2D 33%, #FFD100 33%, #FFD100 66%, #16a34a 66%);
            height: 12px;
            margin: 20px auto 15px;
            border-radius: 2px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 400px;
        }
        
        .company-info {
            font-size: 12px;
            color: #4b5563;
            line-height: 1.8;
        }
        
        .company-info strong {
            color: #16a34a;
        }
        
        /* Badge de reçu */
        .receipt-badge {
            background: linear-gradient(135deg, #EF2B2D 0%, #dc2626 100%);
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 15px 30px;
            margin: 30px 0;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(239, 43, 45, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .receipt-badge::before {
            content: '📋';
            position: absolute;
            left: 20px;
            font-size: 28px;
        }
        
        .receipt-badge::after {
            content: '📋';
            position: absolute;
            right: 20px;
            font-size: 28px;
        }
        
        /* Section informations en grille */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 30px 0;
            padding: 25px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 8px;
            border: 2px solid #bbf7d0;
        }
        
        .info-card {
            background: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .info-card-title {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #16a34a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #6b7280;
            display: flex;
            align-items: center;
        }
        
        .info-label::before {
            content: '▸';
            color: #16a34a;
            margin-right: 8px;
            font-weight: bold;
        }
        
        .info-value {
            color: #1f2937;
            font-weight: 500;
            text-align: right;
        }
        
        /* Badge de statut */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
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
        
        /* Table des produits */
        .products-section {
            margin: 35px 0;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            padding-left: 15px;
            border-left: 5px solid #16a34a;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .products-table thead {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
        }
        
        .products-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        
        .products-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .products-table tbody tr {
            background: white;
            transition: background 0.2s;
        }
        
        .products-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .products-table tbody tr:hover {
            background: #f0fdf4;
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
        
        /* Section totaux */
        .totals-section {
            margin-top: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
            border-radius: 8px;
            border: 2px solid #fdba74;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 15px;
        }
        
        .total-final {
            font-size: 24px;
            font-weight: bold;
            color: #dc2626;
            border-top: 3px solid #16a34a;
            padding-top: 15px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-final::before {
            content: '💰';
            font-size: 28px;
        }
        
        /* Section paiement */
        .payment-section {
            margin: 30px 0;
            padding: 20px;
            background: white;
            border-radius: 8px;
            border-left: 5px solid #16a34a;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .payment-method {
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .payment-method::before {
            content: '💳';
            font-size: 20px;
        }
        
        .payment-method strong {
            color: #16a34a;
        }
        
        /* Section signatures */
        .signatures-section {
            margin-top: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding-top: 30px;
            border-top: 2px dashed #d1d5db;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-title {
            font-weight: bold;
            font-size: 14px;
            color: #16a34a;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .signature-line {
            border-bottom: 2px solid #374151;
            height: 80px;
            margin-bottom: 15px;
            position: relative;
        }
        
        .signature-line::after {
            content: '✍️';
            position: absolute;
            bottom: -15px;
            right: 10px;
            font-size: 20px;
            opacity: 0.3;
        }
        
        .signature-date {
            color: #6b7280;
            font-size: 12px;
            margin-top: 10px;
        }
        
        /* Footer */
        .footer {
            margin-top: 50px;
            text-align: center;
            padding-top: 25px;
            border-top: 3px solid #16a34a;
        }
        
        .footer-flag {
            background: linear-gradient(90deg, #EF2B2D 33%, #FFD100 33%, #FFD100 66%, #16a34a 66%);
            height: 8px;
            margin: 15px auto;
            border-radius: 2px;
            max-width: 300px;
        }
        
        .footer-text {
            font-size: 12px;
            color: #6b7280;
            line-height: 2;
        }
        
        .footer-company {
            font-size: 16px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 8px;
        }
        
        .footer-thanks {
            font-size: 18px;
            color: #dc2626;
            font-weight: bold;
            margin-top: 15px;
            font-style: italic;
        }
        
        /* Bouton d'impression */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.4);
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .print-button:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.5);
        }
        
        .print-button:active {
            transform: translateY(0);
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(22, 163, 74, 0.03);
            font-weight: bold;
            z-index: -1;
            pointer-events: none;
        }
        
        /* Responsive pour mobile */
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .signatures-section {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            
            .receipt-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="watermark">JACKSON ENERGY</div>
    
    <button class="print-button no-print" onclick="window.print()">🖨️ Imprimer le reçu</button>
    
    <div class="receipt-container">
        <!-- Header entreprise -->
        <div class="header">
            <div class="company-logo">{{ $company['name'] ?? 'Jackson Energy International' }}</div>
            <div class="company-tagline">💡 Votre partenaire en solutions énergétiques</div>
            <div class="burkina-flag"></div>
            <div class="company-info">
                <strong>📍 Adresse:</strong> {{ $company['address'] ?? 'Ouagadougou, Burkina Faso' }}<br>
                <strong>📞 Téléphone:</strong> {{ $company['phone'] ?? '+226 XX XX XX XX' }}<br>
                <strong>📧 Email:</strong> {{ $company['email'] ?? 'contact@jackson-energy.bf' }}
            </div>
        </div>

        <!-- Badge titre -->
        <div class="receipt-badge">
            Reçu de Commande Officiel
        </div>

        <!-- Informations en grille -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-card-title">📋 Détails Commande</div>
                <div class="info-row">
                    <span class="info-label">Numéro</span>
                    <span class="info-value"><strong>{{ $order->order_number }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date</span>
                    <span class="info-value">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Statut</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $order->status }}">
                            @switch($order->status)
                                @case('pending') ⏳ En attente @break
                                @case('completed') ✅ Complétée @break
                                @case('cancelled') ❌ Annulée @break
                                @default {{ ucfirst($order->status) }}
                            @endswitch
                        </span>
                    </span>
                </div>
            </div>

            <div class="info-card">
                <div class="info-card-title">👤 Informations Client</div>
                <div class="info-row">
                    <span class="info-label">Nom complet</span>
                    <span class="info-value"><strong>{{ $order->customer_name }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Téléphone</span>
                    <span class="info-value">{{ $order->customer_phone }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email</span>
                    <span class="info-value">{{ $order->customer_email }}</span>
                </div>
            </div>
        </div>

        <!-- Produits commandés -->
        <div class="products-section">
            <div class="section-title">🛒 Produits Commandés</div>
            
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Produit</th>
                        <th class="text-center" style="width: 15%;">Quantité</th>
                        <th class="text-right" style="width: 20%;">Prix Unitaire</th>
                        <th class="text-right" style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td class="product-name">{{ $item->product_name }}</td>
                        <td class="text-center">
                            <strong style="background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 12px; font-size: 13px;">
                                {{ $item->quantity }}×
                            </strong>
                        </td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                        <td class="text-right" style="font-weight: 600; color: #16a34a;">
                            {{ number_format($item->total_price, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Section totaux -->
        <div class="totals-section">
            <div class="total-row">
                <span>Sous-total</span>
                <span style="font-weight: 600;">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($order->discount ?? 0 > 0)
            <div class="total-row">
                <span>Remise</span>
                <span style="color: #dc2626; font-weight: 600;">- {{ number_format($order->discount, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
            <div class="total-final">
                <span>MONTANT TOTAL</span>
                <span>{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        <!-- Mode de paiement -->
        <div class="payment-section">
            <div class="payment-method">
                <strong>Mode de paiement:</strong>
                <span>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            @if($order->payment_status)
            <div class="payment-method" style="margin-top: 10px;">
                <strong>Statut paiement:</strong>
                <span class="status-badge status-{{ $order->payment_status }}">
                    @switch($order->payment_status)
                        @case('paid') ✅ Payé @break
                        @case('pending') ⏳ En attente @break
                        @default {{ ucfirst($order->payment_status) }}
                    @endswitch
                </span>
            </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures-section">
            <div class="signature-box">
                <div class="signature-title">🏢 Signature du Vendeur</div>
                <div class="signature-line"></div>
                <div class="signature-date">
                    Nom: _______________________<br>
                    Date: _______________________
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-title">✍️ Signature du Client</div>
                <div class="signature-line"></div>
                <div class="signature-date">
                    Nom: _______________________<br>
                    Date: _______________________
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-flag"></div>
            <div class="footer-company">{{ $company['name'] ?? 'Jackson Energy International' }}</div>
            <div class="footer-text">
                Solutions énergétiques durables et innovantes 🌍⚡<br>
                NIF: {{ $company['nif'] ?? 'XXXXXXXXXX' }} | RCCM: {{ $company['rccm'] ?? 'BF-XXX-XXX' }}
            </div>
            <div class="footer-thanks">🙏 Merci pour votre confiance !</div>
        </div>
    </div>
    
    <script>
        // Demander l'impression après chargement
        window.addEventListener('load', function() {
            setTimeout(function() {
                if(confirm('📋 Souhaitez-vous imprimer ce reçu maintenant ?')) {
                    window.print();
                }
            }, 1500);
        });
        
        // Raccourci clavier Ctrl+P
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
        
        console.log('✅ Reçu de commande {{ $order->order_number }} chargé');
    </script>
</body>
</html>

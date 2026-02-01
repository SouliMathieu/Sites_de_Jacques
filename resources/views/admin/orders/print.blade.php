<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu - Commande {{ $order->order_number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #008000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #008000;
            margin-bottom: 10px;
        }
        
        .burkina-flag {
            background: linear-gradient(90deg, #EF2B2D 33%, #FFD100 33%, #FFD100 66%, #008000 66%);
            height: 10px;
            margin: 15px 0;
        }
        
        .receipt-title {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin: 25px 0;
            color: #EF2B2D;
            text-transform: uppercase;
        }
        
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        
        .info-box {
            width: 45%;
        }
        
        .label {
            font-weight: bold;
            color: #008000;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        
        .products-table th, .products-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .products-table th {
            background-color: #008000;
            color: white;
            font-weight: bold;
        }
        
        .total-section {
            text-align: right;
            margin-top: 25px;
        }
        
        .total-final {
            font-size: 18px;
            font-weight: bold;
            color: #EF2B2D;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        
        .signature-section {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 40%;
            text-align: center;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 20px;
            color: #008000;
        }
        
        .signature-line {
            border-bottom: 2px solid #333;
            height: 60px;
            margin-bottom: 15px;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #008000;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .print-button:hover {
            background: #006400;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">🖨️ Imprimer</button>
    
    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $company['name'] }}</div>
        <div class="burkina-flag"></div>
        <div>{{ $company['address'] }} | Tél: {{ $company['phone'] }}</div>
    </div>

    <!-- Titre -->
    <div class="receipt-title">Reçu de Commande</div>

    <!-- Informations -->
    <div class="info-section">
        <div class="info-box">
            <p><span class="label">N° Commande:</span> {{ $order->order_number }}</p>
            <p><span class="label">Date:</span> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            <p><span class="label">Statut:</span> {{ ucfirst($order->status) }}</p>
        </div>
        <div class="info-box">
            <p><span class="label">Client:</span> {{ $order->customer_name }}</p>
            <p><span class="label">Téléphone:</span> {{ $order->customer_phone }}</p>
            <p><span class="label">Email:</span> {{ $order->customer_email }}</p>
        </div>
    </div>

    <!-- Produits -->
    <table class="products-table">
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
        <div class="total-final">
            <strong>TOTAL: {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</strong>
        </div>
    </div>

    <!-- Mode de paiement -->
    <p style="margin-top: 20px;"><span class="label">Mode de paiement:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>

    <!-- Signatures -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-title">Signature du Vendeur</div>
            <div class="signature-line"></div>
            <div>Date: ________________</div>
        </div>
        <div class="signature-box">
            <div class="signature-title">Signature du Client</div>
            <div class="signature-line"></div>
            <div>Date: ________________</div>
        </div>
    </div>

    <!-- Footer -->
    <div style="margin-top: 60px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; padding-top: 20px;">
        <div class="burkina-flag"></div>
        <strong>{{ $company['name'] }}</strong> - Solutions énergétiques<br>
        Merci pour votre confiance !
    </div>
    
    <script>
        // Auto-print après 2 secondes
        setTimeout(function() {
            if(confirm('Imprimer le reçu maintenant ?')) {
                window.print();
            }
        }, 2000);
    </script>
</body>
</html>

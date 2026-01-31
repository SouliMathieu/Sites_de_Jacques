<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        if ($request->has('product_id')) {
            $product = Product::findOrFail($request->product_id);
            return view('orders.create', compact('product'));
        }

        return view('orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'customer_company' => 'nullable|string|max:255',
            'delivery_address' => 'required|string',
            'delivery_city' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:orange_money,moov_money,bank_transfer,cash',
            'payment_phone' => 'required_if:payment_method,orange_money,moov_money|nullable|string|max:20',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Créer ou récupérer le client
        $customer = Customer::where('phone', $request->customer_phone)->first();

        if (!$customer) {
            // Si pas de client avec ce téléphone, créer un nouveau
            $customer = Customer::create([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'email' => $request->customer_email,
                'company' => $request->customer_company,
                'address' => $request->delivery_address,
                'city' => $request->delivery_city,
                'country' => 'Burkina Faso',
            ]);
        } else {
            // Si client existe, mettre à jour ses informations
            $customer->update([
                'name' => $request->customer_name,
                'email' => $request->customer_email ?: $customer->email,
                'company' => $request->customer_company ?: $customer->company,
                'address' => $request->delivery_address,
                'city' => $request->delivery_city,
            ]);
        }

        // Calculer le total
        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->products as $productData) {
            $product = Product::findOrFail($productData['id']);
            $quantity = $productData['quantity'];
            $unitPrice = $product->promotional_price ?? $product->price;
            $totalPrice = $unitPrice * $quantity;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ];

            $totalAmount += $totalPrice;
        }

        // Créer la commande avec logique de statut améliorée
        $order = Order::create([
            'order_number' => 'GO-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'customer_id' => $customer->id,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'payment_phone' => $request->payment_phone,
            'notes' => $request->notes,
            'delivery_address' => $request->delivery_address,
            'delivery_city' => $request->delivery_city,
            'delivery_phone' => $request->delivery_phone,
        ]);

        // Créer les articles de commande
        foreach ($orderItems as $item) {
            $order->orderItems()->create($item);
        }

        // Rediriger vers la page de paiement
        return redirect()->route('orders.payment', $order)->with('success', 'Commande créée avec succès !');
    }

    public function payment(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);
        return view('orders.payment', compact('order'));
    }

    public function paymentAtDelivery(Request $request, Order $order)
    {
        $request->validate([
            'payment_at_delivery' => 'required|boolean',
        ]);

        if ($request->payment_at_delivery) {
            // Le client choisit de payer à la livraison
            $order->update([
                'payment_status' => 'pending',
                'status' => 'confirmed',
            ]);

            // Générer l'URL WhatsApp
            $whatsappUrl = $this->generateWhatsAppURL($order, true);

            return redirect()->route('orders.success', $order)->with([
                'success' => 'Commande confirmée ! Paiement à la livraison.',
                'whatsapp_url' => $whatsappUrl,
                'open_whatsapp' => true
            ]);
        }

        // Redirection normale vers la page de paiement
        return redirect()->route('orders.payment', $order);
    }

    public function confirmPayment(Request $request, Order $order)
    {
        $request->validate([
            'payment_reference' => 'required|string|max:255',
        ]);

        $order->update([
            'payment_reference' => $request->payment_reference,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        // Générer l'URL WhatsApp
        $whatsappUrl = $this->generateWhatsAppURL($order);

        return redirect()->route('orders.success', $order)->with([
            'success' => 'Paiement confirmé avec succès !',
            'whatsapp_url' => $whatsappUrl,
            'open_whatsapp' => true
        ]);
    }

    public function success(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);
        return view('orders.success', compact('order'));
    }

    private function generateWhatsAppURL(Order $order, $isPaymentAtDelivery = false)
    {
        $whatsappNumber = '22665033700'; // Numéro correct avec indicatif pays

        if ($isPaymentAtDelivery) {
            $message = $this->generateWhatsAppMessageForDelivery($order);
        } else {
            $message = $this->generateWhatsAppMessage($order);
        }

        // URL WhatsApp avec le message pré-rempli
        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text=" . $message;

        // Log pour debug
        \Log::info("URL WhatsApp générée pour commande {$order->order_number}", [
            'url' => $whatsappUrl,
            'message' => urldecode($message),
            'phone' => $whatsappNumber
        ]);

        return $whatsappUrl;
    }

    private function getPaymentMessage(Order $order)
    {
        $messages = [
            'cash' => 'Votre commande est confirmée. Paiement en espèces à la livraison. Préparez le montant exact.',
            'orange_money' => 'Votre commande est confirmée. Paiement Orange Money à la livraison. Préparez votre téléphone.',
            'moov_money' => 'Votre commande est confirmée. Paiement Moov Money à la livraison. Préparez votre téléphone.',
            'bank_transfer' => 'Votre commande est confirmée. Effectuez le virement selon les instructions ou payez à la livraison.',
        ];

        return $messages[$order->payment_method] ?? 'Commande confirmée.';
    }

    private function generateWhatsAppMessage(Order $order)
    {
        $message = "🎉 *NOUVELLE COMMANDE PAYÉE*\n\n";
        $message .= "📋 *Numéro:* {$order->order_number}\n";
        $message .= "👤 *Client:* {$order->customer->name}\n";
        $message .= "📞 *Téléphone:* {$order->customer->phone}\n";
        $message .= "🏢 *Entreprise:* " . ($order->customer->company ?: 'Particulier') . "\n\n";

        $message .= "📦 *PRODUITS COMMANDÉS:*\n";
        foreach ($order->orderItems as $item) {
            $message .= "• {$item->product->name}\n";
            $message .= " Quantité: {$item->quantity}\n";
            $message .= " Prix unitaire: " . number_format($item->unit_price, 0, ',', ' ') . " FCFA\n";
            $message .= " Sous-total: " . number_format($item->total_price, 0, ',', ' ') . " FCFA\n\n";
        }

        $message .= "💰 *TOTAL:* " . number_format($order->total_amount, 0, ',', ' ') . " FCFA\n\n";

        $message .= "💳 *Paiement:* ";
        switch ($order->payment_method) {
            case 'orange_money':
                $message .= "Orange Money ({$order->payment_phone}) - PAYÉ";
                break;
            case 'moov_money':
                $message .= "Moov Money ({$order->payment_phone}) - PAYÉ";
                break;
            case 'bank_transfer':
                $message .= "Virement bancaire - PAYÉ";
                break;
            case 'cash':
                $message .= "Espèces - PAYÉ";
                break;
        }

        $message .= "\n📄 *Référence:* {$order->payment_reference}\n\n";

        $message .= "🚚 *LIVRAISON:*\n";
        $message .= "📍 *Adresse:* {$order->delivery_address}\n";
        $message .= "🏙️ *Ville:* {$order->delivery_city}\n";
        $message .= "📞 *Téléphone livraison:* {$order->delivery_phone}\n\n";

        if ($order->notes) {
            $message .= "📝 *Notes:* {$order->notes}\n\n";
        }

        $message .= "⏰ *Commandé le:* " . $order->created_at->format('d/m/Y à H:i') . "\n\n";
        $message .= "✅ *Statut:* Confirmée et payée";

        return urlencode($message);
    }

    private function generateWhatsAppMessageForDelivery(Order $order)
    {
        $message = "🚚 *NOUVELLE COMMANDE - PAIEMENT À LA LIVRAISON*\n\n";
        $message .= "📋 *Numéro:* {$order->order_number}\n";
        $message .= "👤 *Client:* {$order->customer->name}\n";
        $message .= "📞 *Téléphone:* {$order->customer->phone}\n";
        $message .= "🏢 *Entreprise:* " . ($order->customer->company ?: 'Particulier') . "\n\n";

        $message .= "📦 *PRODUITS COMMANDÉS:*\n";
        foreach ($order->orderItems as $item) {
            $message .= "• {$item->product->name}\n";
            $message .= " Quantité: {$item->quantity}\n";
            $message .= " Prix unitaire: " . number_format($item->unit_price, 0, ',', ' ') . " FCFA\n";
            $message .= " Sous-total: " . number_format($item->total_price, 0, ',', ' ') . " FCFA\n\n";
        }

        $message .= "💰 *TOTAL À ENCAISSER:* " . number_format($order->total_amount, 0, ',', ' ') . " FCFA\n\n";

        $message .= "💳 *Mode de paiement choisi:* ";
        switch ($order->payment_method) {
            case 'orange_money':
                $message .= "Orange Money à la livraison";
                break;
            case 'moov_money':
                $message .= "Moov Money à la livraison";
                break;
            case 'bank_transfer':
                $message .= "Virement bancaire ou paiement à la livraison";
                break;
            case 'cash':
                $message .= "Espèces à la livraison";
                break;
        }

        if ($order->payment_phone) {
            $message .= " ({$order->payment_phone})";
        }

        $message .= "\n\n🚚 *LIVRAISON:*\n";
        $message .= "📍 *Adresse:* {$order->delivery_address}\n";
        $message .= "🏙️ *Ville:* {$order->delivery_city}\n";
        $message .= "📞 *Téléphone livraison:* {$order->delivery_phone}\n\n";

        if ($order->notes) {
            $message .= "📝 *Notes:* {$order->notes}\n\n";
        }

        $message .= "⏰ *Commandé le:* " . $order->created_at->format('d/m/Y à H:i') . "\n\n";
        $message .= "⚠️ *Statut:* Confirmée - Paiement à la livraison";

        return urlencode($message);
    }
}

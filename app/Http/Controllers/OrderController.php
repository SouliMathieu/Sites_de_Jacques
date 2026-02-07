<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderCreatedNotification;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Afficher le formulaire de création de commande
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $product = null;

        // Pré-sélection d'un produit si fourni
        if ($request->has('product_id')) {
            $product = Product::where('id', $request->product_id)
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->with('category')
                ->firstOrFail();
        }

        // Récupérer les produits récents du client si connecté
        $recentProducts = [];
        if (session()->has('customer_phone')) {
            $customer = Customer::where('phone', session('customer_phone'))->first();
            if ($customer) {
                $recentProducts = $customer->orders()
                    ->with('orderItems.product')
                    ->latest()
                    ->take(3)
                    ->get()
                    ->pluck('orderItems')
                    ->flatten()
                    ->pluck('product')
                    ->unique('id')
                    ->take(5);
            }
        }

        return view('orders.create', compact('product', 'recentProducts'));
    }

    /**
     * Enregistrer une nouvelle commande
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255|min:3',
            'customer_phone' => 'required|string|max:20|regex:/^[\d\s\+\-\(\)]+$/',
            'customer_email' => 'nullable|email|max:255',
            'customer_company' => 'nullable|string|max:255',
            'delivery_address' => 'required|string|min:10|max:500',
            'delivery_city' => 'required|string|max:255',
            'delivery_phone' => 'required|string|max:20|regex:/^[\d\s\+\-\(\)]+$/',
            'payment_method' => 'required|in:orange_money,moov_money,bank_transfer,cash_on_delivery',
            'payment_phone' => 'required_if:payment_method,orange_money,moov_money|nullable|string|max:20',
            'products' => 'required|array|min:1|max:20',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:1000',
        ], [
            'customer_name.required' => 'Le nom complet est obligatoire.',
            'customer_name.min' => 'Le nom doit contenir au moins 3 caractères.',
            'customer_phone.required' => 'Le numéro de téléphone est obligatoire.',
            'customer_phone.regex' => 'Le format du numéro de téléphone n\'est pas valide.',
            'delivery_address.required' => 'L\'adresse de livraison est obligatoire.',
            'delivery_address.min' => 'L\'adresse doit contenir au moins 10 caractères.',
            'payment_method.required' => 'Veuillez sélectionner une méthode de paiement.',
            'products.required' => 'Veuillez sélectionner au moins un produit.',
        ]);

        DB::beginTransaction();

        try {
            // Créer ou mettre à jour le client
            $customer = $this->createOrUpdateCustomer($validated);

            // Calculer le total et préparer les articles
            $orderData = $this->calculateOrderTotal($validated['products']);

            // Vérifier la disponibilité des produits
            $this->checkProductsAvailability($orderData['items']);

            // Créer la commande
            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'customer_company' => $validated['customer_company'],
                'total_amount' => $orderData['total'],
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'payment_phone' => $validated['payment_phone'],
                'notes' => $validated['notes'],
                'delivery_address' => $validated['delivery_address'],
                'delivery_city' => $validated['delivery_city'],
                'delivery_phone' => $validated['delivery_phone'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Créer les articles de commande
            foreach ($orderData['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                // Optionnel : Décrémenter le stock (à activer si souhaité)
                // Product::find($item['product_id'])->decrement('stock_quantity', $item['quantity']);
            }

            DB::commit();

            // Enregistrer le téléphone en session pour faciliter les prochaines commandes
            session(['customer_phone' => $validated['customer_phone']]);

            // Log de la commande
            Log::info("Nouvelle commande créée", [
                'order_number' => $order->order_number,
                'customer' => $customer->name,
                'total' => $order->total_amount,
                'products_count' => count($orderData['items']),
            ]);

            return redirect()->route('orders.payment', $order)
                ->with('success', 'Commande créée avec succès ! Veuillez procéder au paiement.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Erreur lors de la création de commande", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de votre commande. Veuillez réessayer.');
        }
    }

    /**
     * Afficher la page de paiement
     * 
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function payment(Order $order)
    {
        // Vérifier que la commande est bien en attente de paiement
        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.success', $order)
                ->with('info', 'Cette commande a déjà été payée.');
        }

        $order->load(['orderItems.product', 'customer']);

        return view('orders.payment', compact('order'));
    }

    /**
     * Marquer le paiement à la livraison
     * 
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function paymentAtDelivery(Request $request, Order $order)
    {
        $request->validate([
            'payment_at_delivery' => 'required|boolean',
        ]);

        if (!$request->payment_at_delivery) {
            return redirect()->route('orders.payment', $order)
                ->with('error', 'Requête invalide.');
        }

        try {
            $order->update([
                'payment_status' => 'pending',
                'status' => 'confirmed',
                'payment_reference' => 'DELIVERY_' . $order->order_number,
                'confirmed_at' => now(),
            ]);

            Log::info("Commande confirmée avec paiement à la livraison", [
                'order_number' => $order->order_number,
                'payment_method' => $order->payment_method,
            ]);

            // Générer l'URL WhatsApp
            $whatsappUrl = $this->generateWhatsAppURL($order, true);

            return redirect()->route('orders.success', $order)->with([
                'success' => 'Commande confirmée ! Vous paierez à la livraison.',
                'whatsapp_url' => $whatsappUrl,
                'open_whatsapp' => true,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur confirmation paiement à la livraison", [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /**
     * Confirmer le paiement de la commande
     * 
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_reference' => 'required|string|max:255|min:5',
        ], [
            'payment_reference.required' => 'Le numéro de référence du paiement est obligatoire.',
            'payment_reference.min' => 'Le numéro de référence doit contenir au moins 5 caractères.',
        ]);

        try {
            $order->update([
                'payment_reference' => $validated['payment_reference'],
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paid_at' => now(),
                'confirmed_at' => now(),
            ]);

            Log::info("Paiement confirmé pour commande", [
                'order_number' => $order->order_number,
                'payment_method' => $order->payment_method,
                'payment_reference' => $validated['payment_reference'],
            ]);

            // Générer l'URL WhatsApp
            $whatsappUrl = $this->generateWhatsAppURL($order, false);

            // Optionnel : Envoyer une notification email au client
            // Mail::to($order->customer_email)->send(new OrderConfirmedMail($order));

            return redirect()->route('orders.success', $order)->with([
                'success' => 'Paiement confirmé avec succès ! Merci pour votre commande.',
                'whatsapp_url' => $whatsappUrl,
                'open_whatsapp' => true,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur confirmation paiement", [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la confirmation du paiement.');
        }
    }

    /**
     * Afficher la page de succès
     * 
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function success(Order $order)
    {
        $order->load(['orderItems.product', 'customer']);

        // Produits recommandés basés sur la commande
        $recommendedProducts = Product::where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereHas('category', function ($query) use ($order) {
                $categoryIds = $order->orderItems->pluck('product.category_id')->unique();
                $query->whereIn('id', $categoryIds);
            })
            ->whereNotIn('id', $order->orderItems->pluck('product_id'))
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('orders.success', compact('order', 'recommendedProducts'));
    }

    /**
     * Créer ou mettre à jour un client
     * 
     * @param array $data
     * @return Customer
     */
    private function createOrUpdateCustomer(array $data): Customer
    {
        $customer = Customer::where('phone', $data['customer_phone'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'name' => $data['customer_name'],
                'phone' => $data['customer_phone'],
                'email' => $data['customer_email'],
                'company' => $data['customer_company'],
                'address' => $data['delivery_address'],
                'city' => $data['delivery_city'],
                'country' => 'Burkina Faso',
            ]);

            Log::info("Nouveau client créé", ['customer_id' => $customer->id, 'phone' => $customer->phone]);
        } else {
            // Mettre à jour les informations si elles ont changé
            $customer->update([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?: $customer->email,
                'company' => $data['customer_company'] ?: $customer->company,
                'address' => $data['delivery_address'],
                'city' => $data['delivery_city'],
                'last_order_at' => now(),
            ]);

            Log::info("Client existant mis à jour", ['customer_id' => $customer->id]);
        }

        return $customer;
    }

    /**
     * Calculer le total de la commande
     * 
     * @param array $products
     * @return array
     */
    private function calculateOrderTotal(array $products): array
    {
        $totalAmount = 0;
        $orderItems = [];

        foreach ($products as $productData) {
            $product = Product::findOrFail($productData['id']);
            $quantity = (int) $productData['quantity'];
            $unitPrice = $product->promotional_price ?? $product->price;
            $totalPrice = $unitPrice * $quantity;

            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ];

            $totalAmount += $totalPrice;
        }

        return [
            'total' => $totalAmount,
            'items' => $orderItems,
        ];
    }

    /**
     * Vérifier la disponibilité des produits
     * 
     * @param array $items
     * @throws \Exception
     */
    private function checkProductsAvailability(array $items): void
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            
            if (!$product->is_active) {
                throw new \Exception("Le produit {$product->name} n'est plus disponible.");
            }

            if ($product->stock_quantity < $item['quantity']) {
                throw new \Exception("Stock insuffisant pour {$product->name}. Disponible : {$product->stock_quantity}");
            }
        }
    }

    /**
     * Générer un numéro de commande unique
     * 
     * @return string
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'JEI'; // Jackson Energy International
        $date = Carbon::now()->format('Ymd');
        $random = strtoupper(Str::random(6));
        
        $orderNumber = "{$prefix}-{$date}-{$random}";

        // Vérifier l'unicité
        while (Order::where('order_number', $orderNumber)->exists()) {
            $random = strtoupper(Str::random(6));
            $orderNumber = "{$prefix}-{$date}-{$random}";
        }

        return $orderNumber;
    }

    /**
     * Générer l'URL WhatsApp pour notification
     * 
     * @param Order $order
     * @param bool $isPaymentAtDelivery
     * @return string
     */
    private function generateWhatsAppURL(Order $order, bool $isPaymentAtDelivery = false): string
    {
        $whatsappNumber = '22665033700'; // Votre numéro WhatsApp

        $message = $isPaymentAtDelivery 
            ? $this->generateWhatsAppMessageForDelivery($order)
            : $this->generateWhatsAppMessage($order);

        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$message}";

        Log::info("URL WhatsApp générée", [
            'order_number' => $order->order_number,
            'delivery_payment' => $isPaymentAtDelivery,
            'url_length' => strlen($whatsappUrl),
        ]);

        return $whatsappUrl;
    }

    /**
     * Message WhatsApp pour commande payée
     * 
     * @param Order $order
     * @return string
     */
    private function generateWhatsAppMessage(Order $order): string
    {
        $message = "🎉 *NOUVELLE COMMANDE PAYÉE*\n\n";
        $message .= "📋 *Numéro:* {$order->order_number}\n";
        $message .= "👤 *Client:* {$order->customer_name}\n";
        $message .= "📞 *Téléphone:* {$order->customer_phone}\n";
        $message .= "🏢 *Entreprise:* " . ($order->customer_company ?: 'Particulier') . "\n\n";

        $message .= "📦 *PRODUITS COMMANDÉS:*\n";
        foreach ($order->orderItems as $item) {
            $message .= "• {$item->product_name}\n";
            $message .= "  Qté: {$item->quantity} × " . number_format($item->unit_price, 0, ',', ' ') . " FCFA\n";
            $message .= "  Sous-total: " . number_format($item->total_price, 0, ',', ' ') . " FCFA\n\n";
        }

        $message .= "💰 *TOTAL:* " . number_format($order->total_amount, 0, ',', ' ') . " FCFA\n\n";
        
        $message .= "💳 *Paiement:* ";
        $message .= $this->getPaymentMethodLabel($order) . " - ✅ PAYÉ\n";
        $message .= "📄 *Référence:* {$order->payment_reference}\n\n";

        $message .= "🚚 *LIVRAISON:*\n";
        $message .= "📍 {$order->delivery_address}\n";
        $message .= "🏙️ {$order->delivery_city}\n";
        $message .= "📞 {$order->delivery_phone}\n\n";

        if ($order->notes) {
            $message .= "📝 *Notes:* {$order->notes}\n\n";
        }

        $message .= "⏰ *Date:* " . $order->created_at->format('d/m/Y à H:i') . "\n";
        $message .= "✅ *Statut:* Confirmée et payée";

        return rawurlencode($message);
    }

    /**
     * Message WhatsApp pour paiement à la livraison
     * 
     * @param Order $order
     * @return string
     */
    private function generateWhatsAppMessageForDelivery(Order $order): string
    {
        $message = "🚚 *NOUVELLE COMMANDE - PAIEMENT À LA LIVRAISON*\n\n";
        $message .= "📋 *Numéro:* {$order->order_number}\n";
        $message .= "👤 *Client:* {$order->customer_name}\n";
        $message .= "📞 *Téléphone:* {$order->customer_phone}\n";
        $message .= "🏢 *Entreprise:* " . ($order->customer_company ?: 'Particulier') . "\n\n";

        $message .= "📦 *PRODUITS COMMANDÉS:*\n";
        foreach ($order->orderItems as $item) {
            $message .= "• {$item->product_name}\n";
            $message .= "  Qté: {$item->quantity} × " . number_format($item->unit_price, 0, ',', ' ') . " FCFA\n";
            $message .= "  Sous-total: " . number_format($item->total_price, 0, ',', ' ') . " FCFA\n\n";
        }

        $message .= "💰 *TOTAL À ENCAISSER:* " . number_format($order->total_amount, 0, ',', ' ') . " FCFA\n\n";
        
        $message .= "💳 *Mode de paiement:* " . $this->getPaymentMethodLabel($order);
        if ($order->payment_phone) {
            $message .= " ({$order->payment_phone})";
        }
        $message .= "\n\n";

        $message .= "🚚 *LIVRAISON:*\n";
        $message .= "📍 {$order->delivery_address}\n";
        $message .= "🏙️ {$order->delivery_city}\n";
        $message .= "📞 {$order->delivery_phone}\n\n";

        if ($order->notes) {
            $message .= "📝 *Notes:* {$order->notes}\n\n";
        }

        $message .= "⏰ *Date:* " . $order->created_at->format('d/m/Y à H:i') . "\n";
        $message .= "⚠️ *Statut:* Confirmée - Paiement à la livraison";

        return rawurlencode($message);
    }

    /**
     * Obtenir le label de la méthode de paiement
     * 
     * @param Order $order
     * @return string
     */
    private function getPaymentMethodLabel(Order $order): string
    {
        $labels = [
            'orange_money' => 'Orange Money',
            'moov_money' => 'Moov Money',
            'bank_transfer' => 'Virement bancaire',
            'cash_on_delivery' => 'Espèces à la livraison',
        ];

        return $labels[$order->payment_method] ?? $order->payment_method;
    }
}

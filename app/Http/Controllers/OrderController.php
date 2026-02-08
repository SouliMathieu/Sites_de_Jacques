<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Afficher le formulaire de commande (étape 1 : infos).
     * Accepte ?product_id=ID pour pré‑sélectionner un produit.
     */
    public function create(Request $request)
    {
        $product = null;
        $recentProducts = [];

        if ($request->has('product_id')) {
            $product = Product::where('id', $request->product_id)
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->with('category')
                ->firstOrFail();
        }

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
     * Afficher une commande (si route publique utilisée).
     */
    public function show(Order $order)
    {
        $order->load(['orderItems.product', 'customer']);

        return view('orders.show', compact('order'));
    }

    /**
     * Enregistrer une nouvelle commande (étape 1 → 2).
     * Ici on NE choisit PAS encore la méthode de paiement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'      => 'required|string|max:255|min:3',
            'customer_phone'     => 'required|string|max:20|regex:/^[\d\s\+\-\(\)]+$/',
            'customer_email'     => 'nullable|email|max:255',
            'customer_company'   => 'nullable|string|max:255',
            'delivery_address'   => 'required|string|min:10|max:500',
            'delivery_city'      => 'required|string|max:255',
            'delivery_phone'     => 'required|string|max:20|regex:/^[\d\s\+\-\(\)]+$/',
            'products'           => 'required|array|min:1|max:20',
            'products.*.id'      => 'required|exists:products,id',
            'products.*.quantity'=> 'required|integer|min:1|max:100',
            'notes'              => 'nullable|string|max:1000',
        ], [
            'customer_name.required'    => 'Le nom complet est obligatoire.',
            'customer_name.min'         => 'Le nom doit contenir au moins 3 caractères.',
            'customer_phone.required'   => 'Le numéro de téléphone est obligatoire.',
            'customer_phone.regex'      => 'Le format du numéro de téléphone n\'est pas valide.',
            'delivery_address.required' => 'L\'adresse de livraison est obligatoire.',
            'delivery_address.min'      => 'L\'adresse doit contenir au moins 10 caractères.',
            'products.required'         => 'Veuillez sélectionner au moins un produit.',
        ]);

        DB::beginTransaction();

        try {
            // Client
            $customer = $this->createOrUpdateCustomer($validated);

            // Total et lignes
            $orderData = $this->calculateOrderTotal($validated['products']);

            // Disponibilité
            $this->checkProductsAvailability($orderData['items']);

            // Commande SANS infos de paiement pour l’instant
            $order = Order::create([
                'order_number'    => $this->generateOrderNumber(),
                'customer_id'     => $customer->id,
                'customer_name'   => $validated['customer_name'],
                'customer_phone'  => $validated['customer_phone'],
                'customer_email'  => $validated['customer_email'],
                'customer_company'=> $validated['customer_company'],
                'total_amount'    => $orderData['total'],
                'status'          => 'pending',
                'payment_method'  => null,
                'payment_status'  => 'pending',
                'payment_phone'   => null,
                'payment_reference'=> null,
                'notes'           => $validated['notes'],
                'delivery_address'=> $validated['delivery_address'],
                'delivery_city'   => $validated['delivery_city'],
                'delivery_phone'  => $validated['delivery_phone'],
                'ip_address'      => $request->ip(),
                'user_agent'      => $request->userAgent(),
            ]);

            foreach ($orderData['items'] as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity'     => $item['quantity'],
                    'unit_price'   => $item['unit_price'],
                    'total_price'  => $item['total_price'],
                ]);
            }

            DB::commit();

            session(['customer_phone' => $validated['customer_phone']]);

            Log::info('Nouvelle commande créée (étape 1 terminée)', [
                'order_number'  => $order->order_number,
                'customer'      => $customer->name,
                'total'         => $order->total_amount,
                'products_count'=> count($orderData['items']),
            ]);

            // Étape suivante : page de choix / instructions de paiement
            return redirect()
                ->route('orders.payment', $order)
                ->with('success', 'Commande créée ! Choisissez maintenant votre méthode de paiement.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur lors de la création de commande', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création de votre commande. Veuillez réessayer.');
        }
    }

    /**
     * Page de paiement (étape 2).
     * Ici on choisit la méthode et on affiche les boutons Maxit / USSD / etc.
     */
    public function payment(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return redirect()
                ->route('orders.success', $order)
                ->with('info', 'Cette commande a déjà été payée.');
        }

        $order->load(['orderItems.product', 'customer']);

        return view('orders.payment', compact('order'));
    }

    /**
     * Paiement à la livraison (Orange/Moov ou espèces).
     * Étape 2 → 3 sans trans ID.
     */
    public function paymentAtDelivery(Request $request, Order $order)
{
    $validated = $request->validate([
        'payment_method'      => 'required|in:cash_on_delivery,orange_money,moov_money',
        'payment_phone'       => 'nullable|string|max:20',
        'payment_at_delivery' => 'nullable|boolean',
    ]);

    // Utiliser l’opérateur ?? pour éviter l’erreur si la clé n’existe pas
    if (!($validated['payment_at_delivery'] ?? false)) {
        return redirect()
            ->route('orders.payment', $order)
            ->with('error', 'Requête invalide.');
    }

    try {
        $order->update([
            'payment_method'    => $validated['payment_method'],
            'payment_phone'     => $validated['payment_phone'] ?? null,
            'payment_status'    => 'pending',
            'status'            => 'confirmed',
            'payment_reference' => 'DELIVERY_' . $order->order_number,
            'confirmed_at'      => now(),
        ]);

        Log::info('Commande confirmée avec paiement à la livraison', [
            'order_number'   => $order->order_number,
            'payment_method' => $order->payment_method,
        ]);

        $whatsappUrl = $this->generateWhatsAppURL($order, true);

        return redirect()
            ->route('orders.success', $order)
            ->with([
                'success'       => 'Commande confirmée ! Vous paierez à la livraison.',
                'whatsapp_url'  => $whatsappUrl,
                'open_whatsapp' => true,
            ]);

    } catch (\Exception $e) {
        Log::error('Erreur confirmation paiement à la livraison', [
            'order_number' => $order->order_number,
            'error'        => $e->getMessage(),
        ]);

        return back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
    }
}

    /**
     * Confirmer un paiement effectué (Orange, Moov, virement).
     * Le Trans ID (payment_reference) est saisi ici.
     */
    public function confirmPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method'    => 'required|in:orange_money,moov_money,bank_transfer',
            'payment_phone'     => 'nullable|string|max:20',
            'payment_reference' => 'required|string|max:255|min:5',
        ], [
            'payment_reference.required' => 'Le numéro de référence du paiement est obligatoire.',
            'payment_reference.min'      => 'Le numéro de référence doit contenir au moins 5 caractères.',
        ]);

        try {
            $order->update([
                'payment_method'    => $validated['payment_method'],
                'payment_phone'     => $validated['payment_phone'],
                'payment_reference' => $validated['payment_reference'],
                'payment_status'    => 'paid',
                'status'            => 'confirmed',
                'paid_at'           => now(),
                'confirmed_at'      => now(),
            ]);

            Log::info('Paiement confirmé pour commande', [
                'order_number'     => $order->order_number,
                'payment_method'   => $order->payment_method,
                'payment_reference'=> $validated['payment_reference'],
            ]);

            $whatsappUrl = $this->generateWhatsAppURL($order, false);

            return redirect()
                ->route('orders.success', $order)
                ->with([
                    'success'       => 'Paiement confirmé avec succès ! Merci pour votre commande.',
                    'whatsapp_url'  => $whatsappUrl,
                    'open_whatsapp' => true,
                ]);

        } catch (\Exception $e) {
            Log::error('Erreur confirmation paiement', [
                'order_number' => $order->order_number,
                'error'        => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la confirmation du paiement.');
        }
    }

    /**
     * Page de succès (étape 3).
     */
    public function success(Order $order)
    {
        $order->load(['orderItems.product', 'customer']);

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
     * Créer ou mettre à jour un client.
     */
    private function createOrUpdateCustomer(array $data): Customer
    {
        $customer = Customer::where('phone', $data['customer_phone'])->first();

        if (!$customer) {
            $customer = Customer::create([
                'name'    => $data['customer_name'],
                'phone'   => $data['customer_phone'],
                'email'   => $data['customer_email'],
                'company' => $data['customer_company'],
                'address' => $data['delivery_address'],
                'city'    => $data['delivery_city'],
                'country' => 'Burkina Faso',
            ]);

            Log::info('Nouveau client créé', [
                'customer_id' => $customer->id,
                'phone'       => $customer->phone,
            ]);
        } else {
            $customer->update([
                'name'         => $data['customer_name'],
                'email'        => $data['customer_email'] ?: $customer->email,
                'company'      => $data['customer_company'] ?: $customer->company,
                'address'      => $data['delivery_address'],
                'city'         => $data['delivery_city'],
                'last_order_at'=> now(),
            ]);

            Log::info('Client existant mis à jour', [
                'customer_id' => $customer->id,
            ]);
        }

        return $customer;
    }

    /**
     * Calculer le total de la commande.
     */
    private function calculateOrderTotal(array $products): array
    {
        $totalAmount = 0;
        $orderItems  = [];

        foreach ($products as $productData) {
            $product   = Product::findOrFail($productData['id']);
            $quantity  = (int) $productData['quantity'];
            $unitPrice = $product->promotional_price ?? $product->price;
            $totalPrice= $unitPrice * $quantity;

            $orderItems[] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'quantity'     => $quantity,
                'unit_price'   => $unitPrice,
                'total_price'  => $totalPrice,
            ];

            $totalAmount += $totalPrice;
        }

        return [
            'total' => $totalAmount,
            'items' => $orderItems,
        ];
    }

    /**
     * Vérifier la disponibilité des produits.
     */
    private function checkProductsAvailability(array $items): void
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product || !$product->is_active) {
                throw new \Exception("Le produit {$product->name} n'est plus disponible.");
            }

            if ($product->stock_quantity < $item['quantity']) {
                throw new \Exception("Stock insuffisant pour {$product->name}. Disponible : {$product->stock_quantity}");
            }
        }
    }

    /**
     * Générer un numéro de commande unique.
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'JEI';
        $date   = Carbon::now()->format('Ymd');

        do {
            $random      = strtoupper(Str::random(6));
            $orderNumber = "{$prefix}-{$date}-{$random}";
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Générer l'URL WhatsApp.
     */
    private function generateWhatsAppURL(Order $order, bool $isPaymentAtDelivery = false): string
    {
        $whatsappNumber = '22665033700';

        $message = $isPaymentAtDelivery
            ? $this->generateWhatsAppMessageForDelivery($order)
            : $this->generateWhatsAppMessage($order);

        $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$message}";

        Log::info('URL WhatsApp générée', [
            'order_number'     => $order->order_number,
            'delivery_payment' => $isPaymentAtDelivery,
            'url_length'       => strlen($whatsappUrl),
        ]);

        return $whatsappUrl;
    }

    /**
     * Message WhatsApp pour commande payée.
     */
    private function generateWhatsAppMessage(Order $order): string
    {
        $message  = "🎉 *NOUVELLE COMMANDE PAYÉE*\\n\\n";
        $message .= "📋 *Numéro:* {$order->order_number}\\n";
        $message .= "👤 *Client:* {$order->customer_name}\\n";
        $message .= "📞 *Téléphone:* {$order->customer_phone}\\n";
        $message .= "🏢 *Entreprise:* " . ($order->customer_company ?: 'Particulier') . "\\n\\n";

        $message .= "📦 *PRODUITS COMMANDÉS:*\\n";
        foreach ($order->orderItems as $item) {
            $message .= "• {$item->product_name}\\n";
            $message .= " Qté: {$item->quantity} × " . number_format($item->unit_price, 0, ',', ' ') . " FCFA\\n";
            $message .= " Sous-total: " . number_format($item->total_price, 0, ',', ' ') . " FCFA\\n\\n";
        }

        $message .= "💰 *TOTAL:* " . number_format($order->total_amount, 0, ',', ' ') . " FCFA\\n\\n";
        $message .= "💳 *Paiement:* " . $this->getPaymentMethodLabel($order) . " - ✅ PAYÉ\\n";
        $message .= "📄 *Référence:* {$order->payment_reference}\\n\\n";

        $message .= "🚚 *LIVRAISON:*\\n";
        $message .= "📍 {$order->delivery_address}\\n";
        $message .= "🏙️ {$order->delivery_city}\\n";
        $message .= "📞 {$order->delivery_phone}\\n\\n";

        if ($order->notes) {
            $message .= "📝 *Notes:* {$order->notes}\\n\\n";
        }

        $message .= "⏰ *Date:* " . $order->created_at->format('d/m/Y à H:i') . "\\n";
        $message .= "✅ *Statut:* Confirmée et payée";

        return rawurlencode($message);
    }

    /**
     * Message WhatsApp pour paiement à la livraison.
     */
    private function generateWhatsAppMessageForDelivery(Order $order): string
    {
        $message  = "🚚 *NOUVELLE COMMANDE - PAIEMENT À LA LIVRAISON*\\n\\n";
        $message .= "📋 *Numéro:* {$order->order_number}\\n";
        $message .= "👤 *Client:* {$order->customer_name}\\n";
        $message .= "📞 *Téléphone:* {$order->customer_phone}\\n";
        $message .= "🏢 *Entreprise:* " . ($order->customer_company ?: 'Particulier') . "\\n\\n";

        $message .= "📦 *PRODUITS COMMANDÉS:*\\n";
        foreach ($order->orderItems as $item) {
            $message .= "• {$item->product_name}\\n";
            $message .= " Qté: {$item->quantity} × " . number_format($item->unit_price, 0, ',', ' ') . " FCFA\\n";
            $message .= " Sous-total: " . number_format($item->total_price, 0, ',', ' ') . " FCFA\\n\\n";
        }

        $message .= "💰 *TOTAL À ENCAISSER:* " . number_format($order->total_amount, 0, ',', ' ') . " FCFA\\n\\n";
        $message .= "💳 *Mode de paiement:* " . $this->getPaymentMethodLabel($order);
        if ($order->payment_phone) {
            $message .= " ({$order->payment_phone})";
        }

        $message .= "\\n\\n";
        $message .= "🚚 *LIVRAISON:*\\n";
        $message .= "📍 {$order->delivery_address}\\n";
        $message .= "🏙️ {$order->delivery_city}\\n";
        $message .= "📞 {$order->delivery_phone}\\n\\n";

        if ($order->notes) {
            $message .= "📝 *Notes:* {$order->notes}\\n\\n";
        }

        $message .= "⏰ *Date:* " . $order->created_at->format('d/m/Y à H:i') . "\\n";
        $message .= "⚠️ *Statut:* Confirmée - Paiement à la livraison";

        return rawurlencode($message);
    }

    /**
     * Label lisible de la méthode de paiement.
     */
    private function getPaymentMethodLabel(Order $order): string
    {
        $labels = [
            'orange_money'    => 'Orange Money',
            'moov_money'      => 'Moov Money',
            'bank_transfer'   => 'Virement bancaire',
            'cash_on_delivery'=> 'Espèces à la livraison',
        ];

        return $labels[$order->payment_method] ?? $order->payment_method;
    }
}

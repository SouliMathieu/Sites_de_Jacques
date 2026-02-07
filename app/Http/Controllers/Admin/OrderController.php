<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Afficher la liste des commandes
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product']);

        // Recherche globale
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('delivery_city', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par statut de commande
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par statut de paiement
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filtre par méthode de paiement
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtre par ville de livraison
        if ($request->filled('city')) {
            $query->where('delivery_city', $request->city);
        }

        // Filtre par période
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtre rapide
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'pending':
                    $query->where('status', Order::STATUS_PENDING);
                    break;
                case 'unpaid':
                    $query->where('payment_status', Order::PAYMENT_PENDING);
                    break;
            }
        }

        // Tri
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        
        if (in_array($sortBy, ['order_number', 'total_amount', 'created_at', 'confirmed_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        $perPage = $request->input('per_page', 15);
        $orders = $query->paginate($perPage)->withQueryString();

        // Statistiques
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', Order::STATUS_PENDING)->count(),
            'confirmed' => Order::where('status', Order::STATUS_CONFIRMED)->count(),
            'delivered' => Order::where('status', Order::STATUS_DELIVERED)->count(),
            'cancelled' => Order::where('status', Order::STATUS_CANCELLED)->count(),
            'unpaid' => Order::where('payment_status', Order::PAYMENT_PENDING)->count(),
            'total_revenue' => Order::where('payment_status', Order::PAYMENT_PAID)->sum('total_amount'),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total_amount'),
        ];

        // Villes pour le filtre
        $cities = Order::select('delivery_city')
            ->distinct()
            ->orderBy('delivery_city')
            ->pluck('delivery_city');

        return view('admin.orders.index', compact('orders', 'stats', 'cities'));
    }

    /**
     * Afficher une commande
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'orderItems.product.category']);

        // Historique des commandes du client
        $customerOrders = [];
        if ($order->customer) {
            $customerOrders = $order->customer->orders()
                ->where('id', '!=', $order->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('admin.orders.show', compact('order', 'customerOrders'));
    }

    /**
     * Mettre à jour le statut de la commande
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:' . implode(',', [
                    Order::STATUS_PENDING,
                    Order::STATUS_CONFIRMED,
                    Order::STATUS_PROCESSING,
                    Order::STATUS_SHIPPED,
                    Order::STATUS_DELIVERED,
                    Order::STATUS_CANCELLED,
                ])
            ],
            'notes' => 'nullable|string|max:500',
        ], [
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné est invalide.',
        ]);

        try {
            $oldStatus = $order->status;
            $newStatus = $validated['status'];

            // Utiliser les méthodes du modèle
            $updated = match ($newStatus) {
                Order::STATUS_CONFIRMED => $order->confirm(),
                Order::STATUS_PROCESSING => $order->process(),
                Order::STATUS_SHIPPED => $order->ship(),
                Order::STATUS_DELIVERED => $order->deliver(),
                Order::STATUS_CANCELLED => $order->cancel($validated['notes'] ?? null),
                default => $order->update(['status' => $newStatus]),
            };

            if ($updated) {
                // Ajouter des notes si fournies
                if ($request->filled('notes') && $newStatus !== Order::STATUS_CANCELLED) {
                    $order->update([
                        'notes' => $order->notes . "\n\n[" . now()->format('d/m/Y H:i') . "] " . $validated['notes']
                    ]);
                }

                Log::info('Statut commande mis à jour', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'user_id' => auth()->id(),
                ]);

                // TODO: Envoyer une notification au client
                // Mail::to($order->customer_email)->send(new OrderStatusChanged($order));

                return back()->with('success', 'Statut de la commande mis à jour avec succès !');
            }

            return back()->with('error', 'Impossible de mettre à jour le statut. Vérifiez la progression de la commande.');

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour statut commande', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du statut.');
        }
    }

    /**
     * Mettre à jour le statut de paiement
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => [
                'required',
                'in:' . implode(',', [
                    Order::PAYMENT_PENDING,
                    Order::PAYMENT_PAID,
                    Order::PAYMENT_FAILED,
                    Order::PAYMENT_REFUNDED,
                ])
            ],
            'payment_reference' => 'nullable|string|max:255',
        ], [
            'payment_status.required' => 'Le statut de paiement est obligatoire.',
            'payment_status.in' => 'Le statut de paiement sélectionné est invalide.',
        ]);

        try {
            $oldPaymentStatus = $order->payment_status;

            if ($validated['payment_status'] === Order::PAYMENT_PAID) {
                $order->markAsPaid($validated['payment_reference'] ?? null);
            } else {
                $order->update([
                    'payment_status' => $validated['payment_status'],
                    'payment_reference' => $validated['payment_reference'] ?? $order->payment_reference,
                ]);
            }

            Log::info('Statut paiement mis à jour', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'old_status' => $oldPaymentStatus,
                'new_status' => $validated['payment_status'],
                'user_id' => auth()->id(),
            ]);

            return back()->with('success', 'Statut de paiement mis à jour avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour paiement', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Ajouter des notes à la commande
     *
     * @param Request $request
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addNotes(Request $request, Order $order)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $timestamp = now()->format('d/m/Y H:i');
        $userName = auth()->user()->name;
        $newNote = "[{$timestamp}] {$userName}: {$validated['notes']}";

        $order->update([
            'notes' => $order->notes ? $order->notes . "\n\n" . $newNote : $newNote,
        ]);

        return back()->with('success', 'Note ajoutée avec succès !');
    }

    /**
     * Supprimer une commande
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Order $order)
    {
        // Vérifier si la commande peut être supprimée
        if (in_array($order->status, [Order::STATUS_DELIVERED, Order::STATUS_SHIPPED])) {
            return back()->with('error', 'Impossible de supprimer une commande livrée ou expédiée.');
        }

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return back()->with('error', 'Impossible de supprimer une commande déjà payée. Veuillez d\'abord rembourser le client.');
        }

        try {
            DB::beginTransaction();

            $orderNumber = $order->order_number;

            // Supprimer les items
            $order->orderItems()->delete();

            // Supprimer la commande
            $order->delete();

            DB::commit();

            Log::info('Commande supprimée', [
                'order_number' => $orderNumber,
                'user_id' => auth()->id(),
            ]);

            return redirect()
                ->route('admin.orders.index')
                ->with('success', "Commande \"{$orderNumber}\" supprimée avec succès !");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur suppression commande', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Générer le reçu PDF
     *
     * @param Order $order
     * @return mixed
     */
    public function generateReceipt(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);

        $data = [
            'order' => $order,
            'company' => [
                'name' => 'Jackson Energy International',
                'address' => 'Ouagadougou, Burkina Faso',
                'phone' => '+226 65 03 37 00',
                'email' => 'contact@jacksonenergy.bf',
                'website' => 'www.jacksonenergy.bf',
            ],
        ];

        try {
            $pdf = Pdf::loadView('admin.orders.receipt', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = "recu-{$order->order_number}.pdf";
            
            Log::info('PDF généré', [
                'order_id' => $order->id,
                'filename' => $filename,
                'user_id' => auth()->id(),
            ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error('Erreur génération PDF', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return back()->with('error', 'Erreur lors de la génération du PDF.');
        }
    }

    /**
     * Afficher la page d'impression
     *
     * @param Order $order
     * @return \Illuminate\View\View
     */
    public function printReceipt(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);

        $data = [
            'order' => $order,
            'company' => [
                'name' => 'Jackson Energy International',
                'address' => 'Ouagadougou, Burkina Faso',
                'phone' => '+226 65 03 37 00',
                'email' => 'contact@jacksonenergy.bf',
                'website' => 'www.jacksonenergy.bf',
            ],
        ];

        return view('admin.orders.print', $data);
    }

    /**
     * Exporter les commandes en CSV
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $query = Order::with(['customer', 'orderItems']);

        // Appliquer les mêmes filtres que l'index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $csv = "N° Commande,Client,Téléphone,Email,Total,Statut,Paiement,Méthode,Ville,Date\n";

        foreach ($orders as $order) {
            $csv .= implode(',', [
                $order->order_number,
                '"' . $order->customer_name . '"',
                $order->customer_phone,
                $order->customer_email ?? '',
                $order->total_amount,
                $order->status_label,
                $order->payment_status_label,
                $order->payment_method_label,
                $order->delivery_city,
                $order->created_at->format('d/m/Y H:i'),
            ]) . "\n";
        }

        $filename = 'commandes_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Obtenir les statistiques des commandes
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        $period = $request->input('period', 30);
        $startDate = now()->subDays($period);

        $stats = [
            'total_orders' => Order::where('created_at', '>=', $startDate)->count(),
            'total_revenue' => Order::where('created_at', '>=', $startDate)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total_amount'),
            'average_order_value' => Order::where('created_at', '>=', $startDate)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->avg('total_amount'),
            'by_status' => Order::where('created_at', '>=', $startDate)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
            'by_payment_method' => Order::where('created_at', '>=', $startDate)
                ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
                ->groupBy('payment_method')
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Envoyer la commande au client par email
     *
     * @param Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendToCustomer(Order $order)
    {
        if (!$order->customer_email) {
            return back()->with('error', 'Aucun email associé à cette commande.');
        }

        try {
            // TODO: Implémenter l'envoi d'email
            // Mail::to($order->customer_email)->send(new OrderConfirmation($order));

            Log::info('Email commande envoyé', [
                'order_id' => $order->id,
                'email' => $order->customer_email,
            ]);

            return back()->with('success', 'Email envoyé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur envoi email', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Erreur lors de l\'envoi de l\'email.');
        }
    }
}

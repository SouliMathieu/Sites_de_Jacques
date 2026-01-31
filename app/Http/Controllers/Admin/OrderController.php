<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // ✅ AJOUT : Import manquant
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'orderItems.product']);

        // Filtrage par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filtrage par méthode de paiement
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Recherche
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($customerQuery) use ($request) {
                      $customerQuery->where('name', 'like', '%' . $request->search . '%')
                                  ->orWhere('phone', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'orderItems.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut de la commande mis à jour avec succès');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return redirect()->back()->with('success', 'Statut de paiement mis à jour avec succès');
    }
    public function destroy(Order $order)
{
    try {
        DB::beginTransaction();

        $orderNumber = $order->order_number;

        // ✅ NOUVEAU : Activer explicitement les contraintes pour cette session
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        }

        // ✅ CORRECTION : Supprimer manuellement les order_items d'abord
        $order->orderItems()->delete();

        // Ensuite supprimer la commande
        $order->delete();

        DB::commit();

        Log::info('Commande supprimée avec succès', [
            'order_id' => $order->id,
            'order_number' => $orderNumber,
            'admin_user' => auth()->user()->id
        ]);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Commande "' . $orderNumber . '" supprimée avec succès !');

    } catch (\Exception $e) {
        DB::rollback();

        Log::error('Erreur lors de la suppression de la commande', [
            'order_id' => $order->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->route('admin.orders.index')
            ->with('error', 'Erreur lors de la suppression de la commande : ' . $e->getMessage());
    }
}



}

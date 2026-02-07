<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord admin
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Période de comparaison
        $period = $request->input('period', '30'); // 7, 30, 90 jours
        $startDate = now()->subDays($period);
        $previousStartDate = now()->subDays($period * 2);

        // 📊 STATISTIQUES PRINCIPALES
        $stats = $this->getMainStats($startDate, $previousStartDate);

        // 📈 GRAPHIQUES ET TENDANCES
        $charts = $this->getChartData($period);

        // 📦 PRODUITS RÉCENTS
        $recentProducts = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        // ⚠️ PRODUITS EN RUPTURE/STOCK FAIBLE
        $lowStockProducts = Product::where('is_active', true)
            ->where(function ($query) {
                $query->where('stock_quantity', '<', 10)
                      ->orWhere('stock_quantity', '=', 0);
            })
            ->with('category')
            ->orderBy('stock_quantity')
            ->limit(10)
            ->get();

        // 📋 COMMANDES RÉCENTES
        $recentOrders = Order::with(['customer', 'orderItems.product'])
            ->latest()
            ->take(10)
            ->get();

        // 🔔 COMMANDES EN ATTENTE
        $pendingOrders = Order::with(['customer', 'orderItems'])
            ->where('status', Order::STATUS_PENDING)
            ->orWhere('payment_status', Order::PAYMENT_PENDING)
            ->latest()
            ->take(5)
            ->get();

        // 👥 NOUVEAUX CLIENTS
        $newCustomers = Customer::with('orders')
            ->where('created_at', '>=', $startDate)
            ->latest()
            ->take(5)
            ->get();

        // 🏆 TOP PRODUITS (PLUS VENDUS)
        $topSellingProducts = $this->getTopSellingProducts($startDate);

        // 🌟 TOP CLIENTS (PLUS GROS ACHETEURS)
        $topCustomers = $this->getTopCustomers($startDate);

        // 📍 COMMANDES PAR VILLE
        $ordersByCity = $this->getOrdersByCity($startDate);

        // 💳 RÉPARTITION MÉTHODES DE PAIEMENT
        $paymentMethods = $this->getPaymentMethodsStats($startDate);

        // 📊 ACTIVITÉ RÉCENTE
        $recentActivities = $this->getRecentActivities();

        // 🚨 ALERTES ET NOTIFICATIONS
        $alerts = $this->getAlerts();

        return view('admin.dashboard', compact(
            'stats',
            'charts',
            'recentProducts',
            'lowStockProducts',
            'recentOrders',
            'pendingOrders',
            'newCustomers',
            'topSellingProducts',
            'topCustomers',
            'ordersByCity',
            'paymentMethods',
            'recentActivities',
            'alerts',
            'period'
        ));
    }

    /**
     * Obtenir les statistiques principales
     *
     * @param Carbon $startDate
     * @param Carbon $previousStartDate
     * @return array
     */
    private function getMainStats($startDate, $previousStartDate)
    {
        // Période actuelle
        $currentPeriodOrders = Order::where('created_at', '>=', $startDate)->count();
        $currentPeriodRevenue = Order::where('created_at', '>=', $startDate)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->sum('total_amount');

        // Période précédente (pour comparaison)
        $previousPeriodOrders = Order::whereBetween('created_at', [$previousStartDate, $startDate])->count();
        $previousPeriodRevenue = Order::whereBetween('created_at', [$previousStartDate, $startDate])
            ->where('payment_status', Order::PAYMENT_PAID)
            ->sum('total_amount');

        // Calcul des variations
        $ordersChange = $previousPeriodOrders > 0 
            ? (($currentPeriodOrders - $previousPeriodOrders) / $previousPeriodOrders) * 100 
            : 0;

        $revenueChange = $previousPeriodRevenue > 0 
            ? (($currentPeriodRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100 
            : 0;

        return [
            // Produits
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'inactive_products' => Product::where('is_active', false)->count(),
            'featured_products' => Product::where('is_featured', true)->count(),
            'low_stock_products' => Product::where('stock_quantity', '<', 10)->where('is_active', true)->count(),
            'out_of_stock' => Product::where('stock_quantity', '=', 0)->where('is_active', true)->count(),
            'products_with_promo' => Product::whereNotNull('promotional_price')->where('is_active', true)->count(),

            // Catégories
            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'empty_categories' => Category::doesntHave('products')->count(),

            // Commandes
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'confirmed_orders' => Order::where('status', Order::STATUS_CONFIRMED)->count(),
            'processing_orders' => Order::where('status', Order::STATUS_PROCESSING)->count(),
            'shipped_orders' => Order::where('status', Order::STATUS_SHIPPED)->count(),
            'delivered_orders' => Order::where('status', Order::STATUS_DELIVERED)->count(),
            'cancelled_orders' => Order::where('status', Order::STATUS_CANCELLED)->count(),

            // Revenus
            'total_revenue' => Order::where('payment_status', Order::PAYMENT_PAID)->sum('total_amount'),
            'pending_revenue' => Order::where('payment_status', Order::PAYMENT_PENDING)->sum('total_amount'),
            'today_revenue' => Order::whereDate('created_at', today())
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total_amount'),
            'month_revenue' => Order::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->where('payment_status', Order::PAYMENT_PAID)
                ->sum('total_amount'),

            // Clients
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('is_active', true)->count(),
            'vip_customers' => Customer::where('is_vip', true)->count(),
            'new_customers_month' => Customer::whereMonth('created_at', now()->month)->count(),

            // Tendances
            'orders_this_period' => $currentPeriodOrders,
            'revenue_this_period' => $currentPeriodRevenue,
            'orders_change' => round($ordersChange, 1),
            'revenue_change' => round($revenueChange, 1),

            // Moyennes
            'average_order_value' => $currentPeriodOrders > 0 
                ? $currentPeriodRevenue / $currentPeriodOrders 
                : 0,
            'average_items_per_order' => Order::where('created_at', '>=', $startDate)
                ->withCount('orderItems')
                ->get()
                ->avg('order_items_count') ?? 0,

            // Utilisateurs
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'admin_users' => User::admins()->count(),
        ];
    }

    /**
     * Obtenir les données pour les graphiques
     *
     * @param int $days
     * @return array
     */
    private function getChartData($days)
    {
        $startDate = now()->subDays($days);

        // Revenus par jour
        $dailyRevenue = Order::where('created_at', '>=', $startDate)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Commandes par jour
        $dailyOrders = Order::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Commandes par statut
        $ordersByStatus = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Produits par catégorie
        $productsByCategory = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get()
            ->pluck('products_count', 'name')
            ->toArray();

        return [
            'daily_revenue' => $dailyRevenue,
            'daily_orders' => $dailyOrders,
            'orders_by_status' => $ordersByStatus,
            'products_by_category' => $productsByCategory,
        ];
    }

    /**
     * Obtenir les produits les plus vendus
     *
     * @param Carbon $startDate
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopSellingProducts($startDate, $limit = 10)
    {
        return Product::select('products.*')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.payment_status', Order::PAYMENT_PAID)
            ->selectRaw('products.*, SUM(order_items.quantity) as total_sold, SUM(order_items.total_price) as total_revenue')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->with('category')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les meilleurs clients
     *
     * @param Carbon $startDate
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getTopCustomers($startDate, $limit = 10)
    {
        return Customer::select('customers.*')
            ->join('orders', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.payment_status', Order::PAYMENT_PAID)
            ->selectRaw('customers.*, COUNT(orders.id) as orders_count, SUM(orders.total_amount) as total_spent')
            ->groupBy('customers.id')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtenir les commandes par ville
     *
     * @param Carbon $startDate
     * @return array
     */
    private function getOrdersByCity($startDate)
    {
        return Order::where('created_at', '>=', $startDate)
            ->selectRaw('delivery_city, COUNT(*) as count, SUM(total_amount) as revenue')
            ->groupBy('delivery_city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Obtenir les statistiques des méthodes de paiement
     *
     * @param Carbon $startDate
     * @return array
     */
    private function getPaymentMethodsStats($startDate)
    {
        return Order::where('created_at', '>=', $startDate)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total_amount) as total')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => $item->payment_method,
                    'label' => Order::getPaymentMethods()[$item->payment_method] ?? $item->payment_method,
                    'count' => $item->count,
                    'total' => $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Obtenir les activités récentes
     *
     * @param int $limit
     * @return array
     */
    private function getRecentActivities($limit = 10)
    {
        $activities = [];

        // Produits récemment ajoutés
        $recentProducts = Product::latest()->take(3)->get();
        foreach ($recentProducts as $product) {
            $activities[] = [
                'type' => 'product_created',
                'icon' => 'package',
                'color' => 'blue',
                'message' => "Nouveau produit ajouté : {$product->name}",
                'time' => $product->created_at,
            ];
        }

        // Commandes récentes
        $recentOrders = Order::latest()->take(3)->get();
        foreach ($recentOrders as $order) {
            $activities[] = [
                'type' => 'order_created',
                'icon' => 'shopping-cart',
                'color' => 'green',
                'message' => "Nouvelle commande : {$order->order_number}",
                'time' => $order->created_at,
            ];
        }

        // Nouveaux clients
        $recentCustomers = Customer::latest()->take(2)->get();
        foreach ($recentCustomers as $customer) {
            $activities[] = [
                'type' => 'customer_created',
                'icon' => 'user',
                'color' => 'purple',
                'message' => "Nouveau client : {$customer->name}",
                'time' => $customer->created_at,
            ];
        }

        // Trier par date
        usort($activities, function ($a, $b) {
            return $b['time']->timestamp - $a['time']->timestamp;
        });

        return array_slice($activities, 0, $limit);
    }

    /**
     * Obtenir les alertes système
     *
     * @return array
     */
    private function getAlerts()
    {
        $alerts = [];

        // Produits en rupture de stock
        $outOfStockCount = Product::where('stock_quantity', 0)
            ->where('is_active', true)
            ->count();

        if ($outOfStockCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'alert-circle',
                'message' => "{$outOfStockCount} produit(s) en rupture de stock",
                'action' => route('admin.products.index', ['stock' => 'out']),
                'action_text' => 'Voir les produits',
            ];
        }

        // Stock faible
        $lowStockCount = Product::whereBetween('stock_quantity', [1, 9])
            ->where('is_active', true)
            ->count();

        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'alert-triangle',
                'message' => "{$lowStockCount} produit(s) avec stock faible",
                'action' => route('admin.products.index', ['stock' => 'low']),
                'action_text' => 'Voir les produits',
            ];
        }

        // Commandes en attente
        $pendingOrdersCount = Order::where('status', Order::STATUS_PENDING)->count();

        if ($pendingOrdersCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'clock',
                'message' => "{$pendingOrdersCount} commande(s) en attente de traitement",
                'action' => route('admin.orders.index', ['status' => 'pending']),
                'action_text' => 'Voir les commandes',
            ];
        }

        // Paiements en attente
        $pendingPaymentsCount = Order::where('payment_status', Order::PAYMENT_PENDING)
            ->where('status', '!=', Order::STATUS_CANCELLED)
            ->count();

        if ($pendingPaymentsCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'credit-card',
                'message' => "{$pendingPaymentsCount} paiement(s) en attente",
                'action' => route('admin.orders.index', ['payment' => 'pending']),
                'action_text' => 'Voir les commandes',
            ];
        }

        return $alerts;
    }

    /**
     * Exporter les statistiques en PDF
     *
     * @param Request $request
     * @return mixed
     */
    public function exportStats(Request $request)
    {
        // Implémentation avec DomPDF ou autre package
        // return PDF::loadView('admin.reports.stats', $data)->download('rapport.pdf');
    }

    /**
     * Actualiser le cache du dashboard
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshCache()
    {
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.charts');

        return response()->json([
            'success' => true,
            'message' => 'Cache actualisé avec succès !',
        ]);
    }
}

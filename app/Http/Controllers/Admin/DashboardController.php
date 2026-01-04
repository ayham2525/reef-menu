<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // -------------------------------------------
        // Date Range Filter (default: last 30 days)
        // -------------------------------------------
        $fromDate = $request->input('from_date')
            ? Carbon::parse($request->input('from_date'))->startOfDay()
            : now()->subDays(29)->startOfDay();

        $toDate = $request->input('to_date')
            ? Carbon::parse($request->input('to_date'))->endOfDay()
            : now()->endOfDay();

        // Base query for orders in selected period
        $baseQuery = Order::whereBetween('created_at', [$fromDate, $toDate]);

        // -------------------------------------------
        // KPIs (within selected period)
        // -------------------------------------------
        $totalOrders     = (clone $baseQuery)->count();
        $completedOrders = (clone $baseQuery)->where('status', 'completed')->count();
        $pendingOrders   = (clone $baseQuery)->where('status', 'pending')->count();
        $cancelledOrders = (clone $baseQuery)->where('status', 'cancelled')->count();

        $totalRevenue = (clone $baseQuery)
            ->where('status', 'completed')
            ->sum('total_amount');

        $avgOrderValue = $totalOrders ? round($totalRevenue / $totalOrders, 2) : 0;

        // -------------------------------------------
        // Latest Orders (in selected period)
        // -------------------------------------------
        $latestOrders = Order::with('user')
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->latest()
            ->take(6)
            ->get();

        // -------------------------------------------
        // Chart 1: Orders per Day (selected period)
        // -------------------------------------------
        $ordersPerDay = (clone $baseQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => [
                'date'  => $r->date,
                'value' => (int) $r->total,
            ]);

        // -------------------------------------------
        // Chart 2: Revenue per Day (completed only)
        // -------------------------------------------
        $revenuePerDay = (clone $baseQuery)
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => [
                'date'  => $r->date,
                'value' => (float) $r->total,
            ]);

        // -------------------------------------------
        // Chart 3: Top 5 Selling Items
        // Using order_items.item_name
        // -------------------------------------------
        $topItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$fromDate, $toDate])
            // ->where('orders.status', 'completed') // uncomment if you want only completed orders
            ->select('order_items.item_name as label', DB::raw('SUM(order_items.quantity) as value'))
            ->groupBy('order_items.item_name')
            ->orderByDesc('value')
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'label' => $r->label,
                'value' => (int) $r->value,
            ]);

        // -------------------------------------------
        // Chart 4: Orders by Status (for doughnut)
        // -------------------------------------------
        $ordersByStatus = [
            ['label' => __('Completed'), 'value' => $completedOrders],
            ['label' => __('Pending'),   'value' => $pendingOrders],
            ['label' => __('Cancelled'), 'value' => $cancelledOrders],
        ];

        // -------------------------------------------
        // Return to view
        // -------------------------------------------
        return view('admin.dashboard', [
            'totalOrders'     => $totalOrders,
            'completedOrders' => $completedOrders,
            'pendingOrders'   => $pendingOrders,
            'cancelledOrders' => $cancelledOrders,
            'totalRevenue'    => $totalRevenue,
            'avgOrderValue'   => $avgOrderValue,
            'latestOrders'    => $latestOrders,
            'ordersPerDay'    => $ordersPerDay,
            'revenuePerDay'   => $revenuePerDay,
            'topItems'        => $topItems,
            'ordersByStatus'  => $ordersByStatus,
            'fromDate'        => $fromDate,
            'toDate'          => $toDate,
        ]);
    }
}

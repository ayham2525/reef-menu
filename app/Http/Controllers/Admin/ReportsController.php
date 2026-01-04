<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\WarehouseTransfer;
use App\Models\Warehouse;
use App\Models\MenuItem;

class ReportsController extends Controller
{
    /* ============================================================
     * 1️⃣ STOCK LEVEL REPORT
     * ============================================================ */
    public function stockLevel(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $query = InventoryStock::with(['item', 'warehouse'])
            ->orderBy('menu_item_id');

        // Filter: Search
        if ($request->filled('search')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('low') && $request->low == "1") {
            $query->whereColumn('quantity', '<=', 'min_quantity');
        }

        $stocks = $query->paginate(30);

        return view('admin.reports.stock-level', compact('stocks', 'warehouses'));
    }




    /* ============================================================
     * 2️ STOCK MOVEMENT REPORT
     * ============================================================ */
    public function stockMovement(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();
        $items = MenuItem::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();

        $query = InventoryMovement::with(['creator', 'item', 'warehouse'])
            ->latest();

        // Date filters
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Item filter
        if ($request->filled('menu_item_id')) {
            $query->where('menu_item_id', $request->menu_item_id);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // User filter
        if ($request->filled('user_id')) {
            $query->where('created_by', $request->user_id);
        }

        $movements = $query->paginate(40);

        return view('admin.reports.stock-movement', compact(
            'movements',
            'warehouses',
            'items',
            'users'
        ));
    }




    /* ============================================================
     * 3️⃣ STOCK TRANSFER REPORT
     * ============================================================ */
    public function stockTransfers(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $query = WarehouseTransfer::with(['fromWarehouse', 'toWarehouse', 'items'])
            ->latest();

        // Filter: Date Range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Filter: From Warehouse
        if ($request->filled('from_warehouse_id')) {
            $query->where('from_warehouse_id', $request->from_warehouse_id);
        }

        // Filter: To Warehouse
        if ($request->filled('to_warehouse_id')) {
            $query->where('to_warehouse_id', $request->to_warehouse_id);
        }

        // Filter: Status
        if ($request->filled('status') && $request->status != "all") {
            $query->where('status', $request->status);
        }

        $transfers = $query->paginate(30);

        return view('admin.reports.stock-transfers', compact(
            'transfers',
            'warehouses'
        ));
    }
}

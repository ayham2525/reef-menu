<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;

class InventoryStockController extends Controller
{
    /* ----------------------------------------
     * LIST PAGE
     * ---------------------------------------- */
    public function index(Request $request)
    {
        $warehouses = Warehouse::orderBy('name')->get();

        $query = InventoryStock::with(['item', 'warehouse'])
            ->orderBy('is_low_stock', 'desc')
            ->orderBy('quantity');

        //  Search filter
        if ($request->filled('search')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        // Warehouse filter
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $stocks = $query->paginate(15);

        // AJAX request → only return the table partial
        if ($request->ajax()) {
            return view('admin.inventory.partials.table', compact('stocks'))->render();
        }

        return view('admin.inventory.index', compact('stocks', 'warehouses'));
    }

    /* ----------------------------------------
     * SHOW PAGE (DETAIL)
     * ---------------------------------------- */
    public function show(InventoryStock $stock)
    {
        $movements = InventoryMovement::where('menu_item_id', $stock->menu_item_id)
            ->where('warehouse_id', $stock->warehouse_id)
            ->latest()
            ->paginate(20);

        return view('admin.inventory.show', compact('stock', 'movements'));
    }

    /* ----------------------------------------
     * RESTOCK FORM
     * ---------------------------------------- */
    public function restockForm(InventoryStock $stock)
    {
        return view('admin.inventory.restock', compact('stock'));
    }

    public function restock(Request $request, InventoryStock $stock)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'cause' => 'nullable|string|max:255',
        ]);

        // Increase stock using model method
        $stock->increase(
            qty: $request->quantity,
            cause: $request->cause ?: 'Restock',
            userId: auth()->id()
        );

        return redirect()
            ->route('admin.inventory.show', $stock->id)
            ->with('success', 'Stock updated successfully.');
    }

    /* ----------------------------------------
     * ADJUST / WASTE FORM
     * ---------------------------------------- */
    public function adjustForm(InventoryStock $stock)
    {
        return view('admin.inventory.adjust', compact('stock'));
    }

    public function adjust(Request $request, InventoryStock $stock)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'type'     => 'required|in:WASTE,ADJUSTMENT',
            'cause'    => 'nullable|string|max:255',
        ]);

        // Decrease stock using model method
        $stock->decrease(
            qty: $request->quantity,
            cause: $request->cause ?: $request->type,
            userId: auth()->id(),
            type: $request->type       // ADDED: movement type
        );

        return redirect()
            ->route('admin.inventory.show', $stock->id)
            ->with('success', 'Stock adjusted successfully.');
    }
}

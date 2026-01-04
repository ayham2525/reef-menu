<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class WarehouseTransferController extends Controller
{
    public function index()
    {
        return view('admin.transfers.index', [
            'transfers' => WarehouseTransfer::latest()->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.transfers.create', [
            'warehouses' => \App\Models\Warehouse::all(),
            'items'      => \App\Models\MenuItem::all(),
        ]);
    }

    public function store(Request $request)
    {
        // ✅ VALIDATION (FULL)
        $request->validate([
            'from_warehouse_id' => 'required|different:to_warehouse_id',
            'to_warehouse_id'   => 'required',

            'items'                 => 'required|array|min:1',
            'items.*.menu_item_id'  => 'required|exists:menu_items,id',
            'items.*.quantity'      => 'required|numeric|min:0.001',
            'items.*.unit_type'     => 'required|string',
        ]);

        // ✅ CREATE TRANSFER
        $transfer = WarehouseTransfer::create([
            'from_warehouse_id' => $request->from_warehouse_id,
            'to_warehouse_id'   => $request->to_warehouse_id,
            'created_by'        => auth()->id(),
            'status'            => 'draft',
        ]);

        // ✅ SAVE ITEMS SAFELY
        foreach ($request->items as $i) {
            WarehouseTransferItem::create([
                'transfer_id'  => $transfer->id,
                'menu_item_id' => $i['menu_item_id'] ?? null,
                'quantity'     => $i['quantity'] ?? 0,
                'unit_type'    => $i['unit_type'] ?? 'unit',
            ]);
        }

        return redirect()
            ->route('admin.inventory.transfers.index')
            ->with('success', 'Transfer created successfully.');
    }

    public function show(WarehouseTransfer $transfer)
    {
        $transfer->load(['items.item', 'fromWarehouse', 'toWarehouse', 'creator']);

        return view('admin.inventory.transfers.show', [
            'transfer' => $transfer
        ]);
    }

    public function approve(WarehouseTransfer $transfer)
    {
        foreach ($transfer->items as $row) {

            // ❗ Deduct from source warehouse
            InventoryService::deductFromWarehouse(
                warehouseId: $transfer->from_warehouse_id,
                menuItemId: $row->menu_item_id,
                qty: $row->quantity,
                cause: "Warehouse Transfer #{$transfer->id}"
            );

            // ❗ Add to destination warehouse
            InventoryService::addToWarehouse(
                warehouseId: $transfer->to_warehouse_id,
                menuItemId: $row->menu_item_id,
                qty: $row->quantity,
                cause: "Warehouse Transfer #{$transfer->id}"
            );
        }

        $transfer->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'Transfer approved successfully.');
    }

    public function pdf(WarehouseTransfer $transfer)
    {
        $transfer->load(['fromWarehouse', 'toWarehouse', 'items.item', 'creator', 'approver']);

        $pdf = \PDF::loadView('admin.transfers.pdf', [
            'transfer' => $transfer
        ]);

        return $pdf->download("Transfer-{$transfer->id}.pdf");
    }
}

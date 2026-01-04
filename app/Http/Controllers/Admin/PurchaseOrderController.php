<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\MenuItem;
use App\Models\Warehouse;
use Illuminate\Support\Str;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /* ---------------------------------------------
     * INDEX PAGE (with search + status filters)
     * --------------------------------------------- */
    public function index(Request $request)
    {
        $query = PurchaseOrder::query()
            ->with(['vendor', 'warehouse'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;

            $query->where(function ($q) use ($s) {
                $q->where('code', 'LIKE', "%$s%")
                    ->orWhereHas(
                        'vendor',
                        fn($v) =>
                        $v->where('name', 'LIKE', "%$s%")
                    );
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        if ($request->ajax()) {
            return view('admin.purchase-orders.partials.table', compact('orders'))->render();
        }

        return view('admin.purchase-orders.index', compact('orders'));
    }

    /* ---------------------------------------------
     * CREATE
     * --------------------------------------------- */
    public function create()
    {
        return view('admin.purchase-orders.create', [
            'vendors'    => Vendor::all(),
            'warehouses' => Warehouse::all(),
            'items'      => MenuItem::all(),
        ]);
    }

    /* ---------------------------------------------
     * SHOW
     * --------------------------------------------- */
    public function show(PurchaseOrder $purchase_order)
    {
        // Blade expects $po
        $po = $purchase_order;
        return view('admin.purchase-orders.show', compact('po'));
    }

    /* ---------------------------------------------
     * STORE
     * --------------------------------------------- */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_id'     => 'required|exists:vendors,id',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'items'         => 'required|array|min:1',

            'items.*.menu_item_id' => 'nullable|exists:menu_items,id',
            'items.*.item_name'    => 'required_without:items.*.menu_item_id|string',

            'items.*.quantity'     => 'required|numeric|min:0.001',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.unit_type'    => 'required|string',
        ]);

        $po = DB::transaction(function () use ($request) {

            $po = PurchaseOrder::create([
                'vendor_id'     => $request->vendor_id,
                'warehouse_id'  => $request->warehouse_id,
                'status'        => 'draft',
                'created_by'    => auth()->id(),
                'code'          => "PO-" . strtoupper(Str::random(6)),
            ]);

            $total = 0;

            foreach ($request->items as $row) {

                $lineTotal = (float) $row['quantity'] * (float) $row['unit_price'];
                $total += $lineTotal;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'menu_item_id'      => $row['menu_item_id'],
                    'item_name'         => $row['item_name'],
                    'quantity'          => $row['quantity'],
                    'unit_price'        => $row['unit_price'],
                    'unit_type'         => $row['unit_type'],
                    'line_total'        => $lineTotal,
                ]);
            }

            $po->update(['total_amount' => $total]);

            return $po;
        });

        return redirect()
            ->route('admin.purchase-orders.index')
            ->with('success', 'Purchase Order created successfully.');
    }

    /* ---------------------------------------------
     * RECEIVE (CONFIRM GOODS RECEIVED)
     * --------------------------------------------- */
    public function receive(PurchaseOrder $purchase_order)
    {
        foreach ($purchase_order->items as $row) {
            InventoryService::addToWarehouse(
                warehouseId: $purchase_order->warehouse_id,
                menuItemId: $row->menu_item_id,
                qty: $row->quantity,
                cause: "PO #{$purchase_order->code}"
            );
        }

        $purchase_order->update([
            'status'      => 'received',
            'approved_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.purchase-orders.show', $purchase_order)
            ->with('success', 'Goods received successfully.');
    }
}

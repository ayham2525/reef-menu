<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\MenuItemOptionValue;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        // Optional quick search from request (server-side search if needed)
        $query = Order::query();

        if ($search = $request->get('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('agency_name', 'like', "%{$search}%")
                    ->orWhere('total_amount', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the details of a specific order.
     */
    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for manually creating an order (optional).
     */
    public function create()
    {
        // Only active categories with active, available items and active options
        $categories = MenuCategory::active()
            ->ordered()
            ->with([
                'items' => fn($q) => $q->active()->available()->ordered()
                    ->with([
                        'options' => fn($o) => $o->active()->ordered()
                            ->with(['values' => fn($v) => $v->active()->ordered()])
                    ]),
            ])
            ->get();

        return view('admin.orders.create', compact('categories'));
    }

    /**
     * Store a new manually created order.
     */


    public function store(Request $request)
    {
        $data = $request->validate([
            'agency_name'          => 'required|string|max:255',
            'notes'                => 'nullable|string|max:255',

            'items'                => 'required|array|min:1',
            'items.*.item_id'      => 'required|exists:menu_items,id',
            'items.*.item_name'    => 'required|string|max:255',
            'items.*.option_id'    => 'nullable|exists:menu_item_option_values,id',
            'items.*.quantity'     => 'required|numeric|min:1',
            'items.*.unit_price'   => 'required|numeric|min:0',
        ]);

        // Build order items array with correct pricing
        $items = collect($data['items'])->map(function ($i) {
            $qty   = (int) $i['quantity'];
            $unit  = (float) $i['unit_price'];
            $total = round($qty * $unit, 2);

            $item = MenuItem::findOrFail($i['item_id']);

            $optionName = null;
            if (!empty($i['option_id'])) {
                $optionValue = MenuItemOptionValue::find($i['option_id']);
                $optionName = $optionValue ? $optionValue->label : null;
            }

            return [
                'menu_item_id' => $item->id,
                'item_name'    => $item->name,
                'option_id'    => $i['option_id'] ?? null,
                'option_name'  => $optionName,
                'quantity'     => $qty,
                'unit_price'   => $unit,
                'line_total'   => $total,
            ];
        });

        $subtotal = $items->sum('line_total');

        $order = DB::transaction(function () use ($data, $items, $subtotal) {
            $order = new Order();
            $order->agency_name  = $data['agency_name'];
            $order->total_amount = round($subtotal, 2);
            $order->status       = 'pending';
            $order->notes        = $data['notes'] ?? 'Created from Admin';
            $order->placed_at    = now();
            $order->save();

            foreach ($items as $row) {
                OrderItem::create($row + ['order_id' => $order->id]);
            }

            return $order;
        });

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', __('Order created successfully.'));
    }


    /**
     * Update single order status (AJAX).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:new,processing,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        $order->save();

        return response()->json(['success' => true]);
    }

    /**
     * Bulk update multiple order statuses (AJAX).
     */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'status' => 'required|string|in:new,processing,completed,cancelled',
        ]);

        $ids = $request->input('ids');
        $status = $request->input('status');

        if (!empty($ids)) {
            Order::whereIn('id', $ids)->update(['status' => $status]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Optional destroy method (if you plan to support delete).
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', __('Order deleted successfully!'));
    }

    public function getItemsByCategory($categoryId)
    {
        $category = MenuCategory::with('items')->findOrFail($categoryId);
        return response()->json(['items' => $category->items->map(fn($i) => [
            'id' => $i->id,
            'name' => $i->name,
            'price' => $i->price,
        ])]);
    }

    public function getItemOptions($itemId)
    {
        $item = \App\Models\MenuItem::with('options')->findOrFail($itemId);
        return response()->json(['options' => $item->options->map(fn($o) => [
            'id' => $o->id,
            'name' => $o->name,
            'extra_price' => $o->extra_price,
        ])]);
    }
}

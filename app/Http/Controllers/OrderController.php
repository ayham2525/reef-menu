<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        try {
            $data = $request->validated();

            // Recalculate items server-side
            $items = collect($data['items'])->map(function ($i) {
                $qty   = (int) $i['quantity'];
                $unit  = (float) $i['unit_price'];
                $total = round($qty * $unit, 2);

                return [
                    'item_name'     => $i['item_name'],
                    'quantity'      => $qty,
                    'unit_price'    => $unit,
                    'line_total'    => $total,
                    'menu_item_id'  => $i['menu_item_id'] ?? null,
                ];
            });

            $totalAmount = round($items->sum('line_total'), 2);

            $order = DB::transaction(function () use ($data, $items, $totalAmount) {

                $order = new Order();

                // ✅ If your Order model does NOT auto UUID, set it here
                if (empty($order->id)) {
                    $order->id = (string) Str::uuid();
                }

                // ✅ Ensure code exists (because migration has code NOT NULL + unique)
                if (empty($order->code)) {
                    do {
                        $code = 'ORD-' . strtoupper(Str::random(8));
                    } while (Order::where('code', $code)->exists());

                    $order->code = $code;
                }

                $order->employee_id  = $data['employee_id'] ?? null;
                $order->broker_id    = $data['broker_id'] ?? null;

                // ✅ Save agency name
                $order->agency_name  = $data['agency_name'];

                $order->is_free      = (bool) ($data['is_free'] ?? false);
                $order->total_amount = $totalAmount;
                $order->status       = 'pending';
                $order->notes        = $data['notes'] ?? 'Order placed from Web Menu';
                $order->placed_at    = now();
                $order->save();

                foreach ($items as $row) {
                    $orderItem = OrderItem::create([
                        'order_id'   => $order->id,
                        'item_name'  => $row['item_name'],
                        'quantity'   => $row['quantity'],
                        'unit_price' => $row['unit_price'],
                        'line_total' => $row['line_total'],
                    ]);

                    // Inventory deduction (optional)
                    // if (!empty($row['menu_item_id'])) {
                    //     InventoryService::deductForOrder(
                    //         orderItem: $orderItem,
                    //         menuItemId: $row['menu_item_id']
                    //     );
                    // }
                }

                return $order;
            });

            return response()->json([
                'id'           => $order->id,
                'code'         => $order->code,
                'status'       => $order->status,
                'total_amount' => (float) $order->total_amount,
                'agency_name'  => $order->agency_name,
            ]);
        } catch (\Throwable $e) {
            report($e); // ✅ log it

            return response()->json([
                'message' => config('app.debug') ? $e->getMessage() : 'Order creation failed',
            ], 500);
        }
    }
}

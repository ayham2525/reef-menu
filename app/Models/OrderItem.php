<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'item_name', 'quantity', 'unit_price', 'line_total'];

    protected $casts = [
        'quantity'    => 'integer',
        'unit_price'  => 'decimal:2',
        'line_total'  => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Auto-calc line_total if not provided
    protected static function booted()
    {
        static::created(function (OrderItem $item) {

            $menuItem = MenuItem::where('name', $item->item_name)->first();

            if ($menuItem && $menuItem->stock) {
                $menuItem->stock->decrease(
                    qty: $item->quantity,
                    cause: 'Order #' . $item->order_id,
                    userId: $item->order->user_id,
                );
            }
        });
    }
}

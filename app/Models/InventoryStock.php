<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\LowStockEvent;

class InventoryStock extends Model
{
    protected $fillable = [
        'menu_item_id',
        'warehouse_id',

        // professional stock fields
        'unit_type',
        'unit_multiplier',
        'quantity',
        'min_quantity',

        // alert system
        'is_low_stock',
        'low_stock_notified_at',

        'last_restocked_at',
        'updated_by',
    ];

    protected $casts = [
        'quantity'           => 'decimal:3',
        'min_quantity'       => 'decimal:3',
        'unit_multiplier'    => 'decimal:2',
        'is_low_stock'       => 'boolean',
        'last_restocked_at'  => 'datetime',
        'low_stock_notified_at' => 'datetime',
    ];

    /* --------------------------------
     * Relationships
     * -------------------------------- */
    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /* --------------------------------
     * Helper: Base stock formatter
     * -------------------------------- */
    public function formatQuantity()
    {
        return "{$this->quantity} {$this->unit_type}";
    }

    /* --------------------------------
     * STOCK INCREASE
     * -------------------------------- */
    public function increase(float $qty, string $cause = 'Restock', ?int $userId = null)
    {
        $this->quantity = $this->quantity + ($qty * $this->unit_multiplier);
        $this->last_restocked_at = now();
        $this->updated_by = $userId;
        $this->is_low_stock = false;
        $this->low_stock_notified_at = null;
        $this->save();

        InventoryMovement::record(
            itemId: $this->menu_item_id,
            type: 'IN',
            qty: $qty,
            cause: $cause,
            warehouseId: $this->warehouse_id,
            userId: $userId,
        );
    }

    /* --------------------------------
     * STOCK DECREASE
     * -------------------------------- */
    public function decrease(float $qty, string $cause = 'Order Deduction', ?int $userId = null)
    {
        $this->quantity = max(0, $this->quantity - ($qty * $this->unit_multiplier));
        $this->updated_by = $userId;
        $this->save();

        $this->checkLowStock();

        InventoryMovement::record(
            itemId: $this->menu_item_id,
            type: 'OUT',
            qty: $qty,
            cause: $cause,
            warehouseId: $this->warehouse_id,
            userId: $userId,
        );
    }

    /* --------------------------------
     * LOW STOCK CHECK
     * -------------------------------- */
    public function checkLowStock()
    {
        if ($this->quantity <= $this->min_quantity && !$this->is_low_stock) {
            $this->is_low_stock = true;
            $this->low_stock_notified_at = now();
            $this->save();

            event(new LowStockEvent($this));
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $fillable = [
        'menu_item_id',
        'warehouse_id',
        'type',          // IN, OUT, WASTE, ADJUSTMENT
        'quantity',      // decimal supported
        'cause',         // e.g. "Order #123", "Restock", "Waste"
        'reference_id',  // order_id or other reference
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'created_by' => 'integer',
        'menu_item_id' => 'integer',
        'warehouse_id' => 'integer',
        'reference_id' => 'integer',
    ];

    /* ------------------------------------
     * Relationships
     * ------------------------------------ */

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ------------------------------------
     * Static helper for clean movement logs
     * ------------------------------------ */

    public static function record(
        int $itemId,
        string $type,
        float $qty,
        ?string $cause = null,
        ?int $warehouseId = null,
        ?int $refId = null,
        ?int $userId = null
    ) {
        return self::create([
            'menu_item_id' => $itemId,
            'warehouse_id' => $warehouseId,
            'type'         => strtoupper($type),
            'quantity'     => $qty,
            'cause'        => $cause,
            'reference_id' => $refId,
            'created_by'   => $userId,
        ]);
    }
}

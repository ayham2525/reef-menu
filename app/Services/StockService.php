<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;

class StockService
{
    /**
     * Increase stock for a specific menu item in a specific warehouse
     */
    public static function increase(
        int $menuItemId,
        int $warehouseId,
        float $qty,
        string $cause = 'Restock',
        ?int $userId = null
    ) {
        // Get or Create stock row
        $stock = InventoryStock::firstOrCreate(
            [
                'menu_item_id'  => $menuItemId,
                'warehouse_id'  => $warehouseId,
            ],
            [
                'quantity'      => 0,
                'min_quantity'  => 0,
            ]
        );

        // Update stock
        $stock->quantity += $qty;
        $stock->save();

        // Log movement
        InventoryMovement::create([
            'menu_item_id' => $menuItemId,
            'warehouse_id' => $warehouseId,
            'type'         => 'IN',
            'quantity'     => $qty,
            'cause'        => $cause,
            'created_by'   => $userId,
        ]);
    }

    /**
     * Decrease stock (OUT, WASTE, ADJUSTMENT)
     */
    public static function decrease(
        int $menuItemId,
        int $warehouseId,
        float $qty,
        string $cause = 'Usage',
        string $type = 'OUT',   // OUT | WASTE | ADJUSTMENT
        ?int $userId = null
    ) {
        $stock = InventoryStock::where('menu_item_id', $menuItemId)
            ->where('warehouse_id', $warehouseId)
            ->firstOrFail();

        $stock->quantity -= $qty;
        $stock->save();

        InventoryMovement::create([
            'menu_item_id' => $menuItemId,
            'warehouse_id' => $warehouseId,
            'type'         => $type,
            'quantity'     => $qty,
            'cause'        => $cause,
            'created_by'   => $userId,
        ]);
    }
}

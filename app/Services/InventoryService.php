<?php

namespace App\Services;

use App\Models\InventoryStock;

class InventoryService
{
    public static function deductForOrder($orderItem)
    {
        $menuItem = $orderItem->menuItem ?? null;
        if (!$menuItem || $menuItem->recipe->isEmpty()) return;

        foreach ($menuItem->recipe as $ingredient) {

            // ingredient total quantity for this order
            $totalQty = $ingredient->quantity * $orderItem->quantity;

            // find stock record
            $stock = InventoryStock::where('menu_item_id', $ingredient->ingredient_item_id)
                ->where('warehouse_id', $ingredient->warehouse_id)
                ->first();

            if ($stock) {
                $stock->decrease(
                    qty: $totalQty,
                    cause: "Order #{$orderItem->order_id} - Ingredient",
                    userId: auth()->id() ?? null
                );
            }
        }
    }

    public static function deductFromWarehouse($warehouseId, $menuItemId, $qty, $cause)
    {
        $stock = InventoryStock::where('warehouse_id', $warehouseId)
            ->where('menu_item_id', $menuItemId)
            ->first();

        if ($stock) {
            $stock->decrease($qty, $cause, auth()->id());
        }
    }

    public static function addToWarehouse($warehouseId, $menuItemId, $qty, $cause)
    {
        $stock = InventoryStock::firstOrCreate([
            'warehouse_id' => $warehouseId,
            'menu_item_id' => $menuItemId,
        ]);

        $stock->increase($qty, $cause, auth()->id());
    }
}

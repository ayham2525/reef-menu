<?php

namespace App\Events;

use App\Models\InventoryStock;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockEvent
{
    use Dispatchable, SerializesModels;

    public InventoryStock $stock;

    public function __construct(InventoryStock $stock)
    {
        $this->stock = $stock;
    }
}

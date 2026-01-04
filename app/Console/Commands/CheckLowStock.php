<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryStock;
use App\Events\LowStockEvent;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check-low';
    protected $description = 'Daily scan for low stock items';

    public function handle()
    {
        $stocks = InventoryStock::whereColumn('quantity', '<=', 'min_quantity')
            ->where('is_low_stock', false)
            ->get();

        foreach ($stocks as $stock) {
            event(new LowStockEvent($stock));
            $this->info("Low stock notified: " . $stock->item->name);
        }

        return 0;
    }
}

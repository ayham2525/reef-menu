<?php

namespace App\Listeners;

use App\Events\LowStockEvent;
use App\Mail\LowStockMail;
use Illuminate\Support\Facades\Mail;

class SendLowStockNotification
{
    public function handle(LowStockEvent $event)
    {
        $stock = $event->stock;

        // Prevent sending twice
        if ($stock->low_stock_notified_at) {
            return;
        }

        // Mark notification timestamp
        $stock->low_stock_notified_at = now();
        $stock->save();

        // Send email
        Mail::to('admin@reefmenu.ae')
            ->send(new LowStockMail($stock));
    }
}

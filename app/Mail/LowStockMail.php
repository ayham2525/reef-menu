<?php

namespace App\Mail;

use App\Models\InventoryStock;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public InventoryStock $stock;

    public function __construct(InventoryStock $stock)
    {
        $this->stock = $stock;
    }

    public function build()
    {
        return $this->subject('Low Stock Alert: ' . $this->stock->item->name)
            ->markdown('emails.low_stock');
    }
}

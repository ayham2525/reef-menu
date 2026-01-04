<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransferItem extends Model
{
    protected $fillable = [
        'transfer_id',
        'menu_item_id',
        'quantity',
        'unit_type',
    ];

    public function transfer()
    {
        return $this->belongsTo(WarehouseTransfer::class);
    }

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}

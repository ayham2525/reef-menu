<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseTransfer extends Model
{
    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'status',
        'created_by',
        'approved_by',
    ];

    public function from()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function to()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }



    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseTransferItem::class, 'transfer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

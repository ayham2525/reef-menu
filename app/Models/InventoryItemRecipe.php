<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItemRecipe extends Model
{
    protected $fillable = [
        'menu_item_id',
        'ingredient_item_id',
        'quantity',
        'unit_type',
        'warehouse_id',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(MenuItem::class, 'ingredient_item_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}

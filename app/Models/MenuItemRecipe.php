<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemRecipe extends Model
{
    protected $fillable = [
        'menu_item_id',
        'ingredient_name',
        'quantity',
        'unit_type',
        'sort_order',
    ];

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }
}

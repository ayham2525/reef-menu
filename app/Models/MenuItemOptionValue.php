<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemOptionValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_option_id',
        'label',
        'price_delta',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price_delta' => 'decimal:2',
        'is_default'  => 'boolean',
        'sort_order'  => 'integer',
        'is_active'   => 'boolean',
    ];

    /* ---------------- Relationships ---------------- */

    public function option()
    {
        return $this->belongsTo(MenuItemOption::class, 'menu_item_option_id');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    /* ---------------- Hooks ---------------- */

    protected static function booted()
    {
        static::saving(function (MenuItemOptionValue $val) {
            $val->sort_order = (int) ($val->sort_order ?? 0);
        });

        static::saved(function (MenuItemOptionValue $val) {
            // If default is set on a 'single' option, unset on siblings
            if ($val->is_default && $val->option && $val->option->type === 'single') {
                static::where('menu_item_option_id', $val->menu_item_option_id)
                    ->where('id', '!=', $val->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}

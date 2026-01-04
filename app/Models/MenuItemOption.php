<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'name',
        'type',          // 'single' | 'multiple'
        'is_required',
        'min_choices',
        'max_choices',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'min_choices' => 'integer',
        'max_choices' => 'integer',
        'sort_order'  => 'integer',
        'is_active'   => 'boolean',
    ];

    /* ---------------- Relationships ---------------- */

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function values()
    {
        return $this->hasMany(MenuItemOptionValue::class, 'menu_item_option_id')
            ->orderBy('sort_order')->orderBy('id');
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
        static::saving(function (MenuItemOption $opt) {
            $opt->type = in_array($opt->type, ['single', 'multiple'], true) ? $opt->type : 'single';
            $opt->sort_order = (int) ($opt->sort_order ?? 0);

            if ($opt->type === 'single') {
                // Radio-like: at most 1 choice; required => min 1 else 0
                $opt->max_choices = 1;
                $opt->min_choices = $opt->is_required ? 1 : 0;
            } else {
                // Checkbox-like: keep sane bounds
                $min = (int) ($opt->min_choices ?? 0);
                $max = (int) ($opt->max_choices ?? 0);
                if ($max > 0 && $min > $max) {
                    $min = $max;
                }
                $opt->min_choices = max(0, $min);
                $opt->max_choices = $max ?: null; // null = unlimited
            }
        });
    }
}

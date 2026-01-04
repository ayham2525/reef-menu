<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MenuItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'disk',
        'path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* ---------------- Relationships ---------------- */

    public function item()
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function scopePrimary($q)
    {
        return $q->where('is_primary', true);
    }

    /* ---------------- Accessors ---------------- */

    public function getUrlAttribute(): ?string
    {
        if (!$this->path) return null;
        // If already absolute (CDN/external), return as-is
        if (preg_match('~^https?://~i', $this->path)) return $this->path;

        $disk = $this->disk ?: 'public';
        return Storage::disk($disk)->url($this->path);
    }

    /* ---------------- Hooks ---------------- */

    protected static function booted()
    {
        static::creating(function (MenuItemImage $img) {
            // Default disk and sort order (append to end if not provided)
            $img->disk = $img->disk ?: 'public';
            if (is_null($img->sort_order) && $img->menu_item_id) {
                $max = static::where('menu_item_id', $img->menu_item_id)->max('sort_order');
                $img->sort_order = is_null($max) ? 0 : ($max + 10);
            }
        });

        static::saving(function (MenuItemImage $img) {
            $img->disk = $img->disk ?: 'public';
            // keep sort_order numeric
            $img->sort_order = (int) ($img->sort_order ?? 0);
        });

        static::saved(function (MenuItemImage $img) {
            // Ensure only one primary per item
            if ($img->is_primary) {
                static::where('menu_item_id', $img->menu_item_id)
                    ->where('id', '!=', $img->id)
                    ->update(['is_primary' => false]);
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MenuCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'parent_id',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /* ---------------- Relationships ---------------- */

    public function parent()
    {
        return $this->belongsTo(MenuCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuCategory::class, 'parent_id')
            ->orderBy('sort_order')->orderBy('name');
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class)
            ->orderBy('sort_order')->orderBy('name');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeRoot($q)
    {
        return $q->whereNull('parent_id');
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    /* ---------------- Hooks ---------------- */

    protected static function booted()
    {
        static::creating(function (MenuCategory $c) {
            if (empty($c->slug)) {
                $c->slug = Str::slug($c->name);
            }
        });

        static::saving(function (MenuCategory $c) {
            // keep slug normalized (fallback to name)
            $c->slug = Str::slug($c->slug ?: $c->name);
        });
    }
}

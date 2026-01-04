<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Section extends Model
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
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /* ---------------- Relationships ---------------- */

    public function parent()
    {
        return $this->belongsTo(Section::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Section::class, 'parent_id')
            ->orderBy('sort_order')->orderBy('name');
    }

    // Later: connect to positions and users
    // public function positions() { return $this->hasMany(Position::class); }
    // public function users() { return $this->hasMany(User::class); }

    /* ---------------- Scopes ---------------- */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeRoot($q)
    {
        return $q->whereNull('parent_id');
    }

    /* ---------------- Hooks ---------------- */

    protected static function booted()
    {
        static::creating(function (Section $s) {
            // auto slug if not provided
            if (empty($s->slug)) {
                $s->slug = Str::slug($s->name);
            }
        });

        static::saving(function (Section $s) {
            // keep slug normalized
            $s->slug = Str::slug($s->slug ?: $s->name);
        });
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}

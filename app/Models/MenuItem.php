<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'menu_category_id',
        'name',
        'slug',
        'sku',
        'description',
        'price',
        'currency',
        'is_featured',
        'is_available',
        'is_active',
        'prep_time_minutes',
        'calories',
        'tags',
        'allergens',
        'sort_order',
        'created_by',
        'updated_by',

        // single image fallback (if you prefer to store one directly)
        'image_path',
        'image_disk',
        'stock_quantity',
        'min_stock_alert'
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'is_featured'       => 'boolean',
        'is_available'      => 'boolean',
        'is_active'         => 'boolean',
        'prep_time_minutes' => 'integer',
        'calories'          => 'integer',
        'tags'              => 'array',
        'allergens'         => 'array',
        'sort_order'        => 'integer',
    ];

    /* ---------------- Relationships ---------------- */

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function images()
    {
        return $this->hasMany(MenuItemImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(MenuItemImage::class)->where('is_primary', true);
    }

    public function options()
    {
        return $this->hasMany(MenuItemOption::class)->orderBy('sort_order');
    }

    public function optionValues()
    {
        return $this->hasManyThrough(MenuItemOptionValue::class, MenuItemOption::class);
    }

    /* ---------------- Scopes ---------------- */

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
    public function scopeAvailable($q)
    {
        return $q->where('is_available', true);
    }
    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }
    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    public function scopeSearch($q, ?string $s)
    {
        if (!$s) return $q;
        $s = trim($s);
        return $q->where(function ($qq) use ($s) {
            $qq->where('name', 'like', "%{$s}%")
                ->orWhere('sku', 'like', "%{$s}%")
                ->orWhere('slug', 'like', "%{$s}%");
        });
    }

    /* ---------------- Accessors / Helpers ---------------- */

    /**
     * Get the primary image URL (via relation or fallback to image_path).
     */
    // app/Models/MenuItem.php

    /**
     * Computed primary image URL
     * DB column: image_path (menu_items/xxx.png)
     * Physical file: public/storage/menu_items/xxx.png
     */
    public function getPrimaryImageUrlAttribute(): ?string
    {
        // 1️⃣ Prefer relation if you use multiple images later
        if ($this->relationLoaded('primaryImage') && $this->primaryImage) {
            return url('public/storage/' . ltrim($this->primaryImage->path, '/'));
        }

        // 2️⃣ Fallback to single image column
        if ($this->image_path) {
            return url('public/storage/' . ltrim($this->image_path, '/'));
        }

        return null;
    }





    /**
     * Helper: check if item has any image.
     */
    public function hasImage(): bool
    {
        return !empty($this->primary_image_url);
    }

    /* ---------------- Hooks ---------------- */

    protected static function booted()
    {
        static::creating(function (MenuItem $m) {
            if (empty($m->slug)) {
                $m->slug = Str::slug($m->name);
            }
            $m->currency = strtoupper($m->currency ?: 'AED');
        });

        static::saving(function (MenuItem $m) {
            $m->slug     = Str::slug($m->slug ?: $m->name);
            $m->currency = strtoupper($m->currency ?: 'AED');

            if (is_array($m->tags) && empty($m->tags)) {
                $m->tags = null;
            }
            if (is_array($m->allergens) && empty($m->allergens)) {
                $m->allergens = null;
            }
        });

        /**
         * Create a stock row automatically for each new menu item
         */
        static::created(function (MenuItem $item) {
            InventoryStock::create([
                'menu_item_id' => $item->id,
                'quantity'     => 0,
                'min_quantity' => 0,
            ]);
        });
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function stock()
    {
        return $this->hasOne(InventoryStock::class, 'menu_item_id');
    }

    public function getStockQuantityAttribute(): int
    {
        return $this->stock->quantity ?? 0;
    }
    public function recipe()
    {
        return $this->hasMany(MenuItemRecipe::class)->orderBy('sort_order');
    }

    public function increaseStock(float $qty, int $warehouseId, string $cause = null, ?int $userId = null): void
    {
        // Update item stock quantity
        $this->increment('stock_quantity', $qty);

        // Create movement log
        InventoryMovement::create([
            'menu_item_id' => $this->id,
            'warehouse_id' => $warehouseId,
            'type'         => 'IN',
            'quantity'     => $qty,
            'cause'        => $cause,
            'reference_id' => null,
            'created_by'   => $userId ?? auth()->id(),
        ]);
    }

    public function decreaseStock(float $qty, int $warehouseId, string $cause = null, ?int $userId = null): void
    {
        // Prevent negative stock
        $newStock = max(0, $this->stock_quantity - $qty);
        $usedQty  = $this->stock_quantity <= 0 ? 0 : $qty;

        $this->update(['stock_quantity' => $newStock]);

        InventoryMovement::create([
            'menu_item_id' => $this->id,
            'warehouse_id' => $warehouseId,
            'type'         => 'OUT',
            'quantity'     => $usedQty,
            'cause'        => $cause,
            'reference_id' => null,
            'created_by'   => $userId ?? auth()->id(),
        ]);
    }

    public function wasteStock(float $qty, int $warehouseId, string $cause = 'Waste', ?int $userId = null): void
    {
        $newStock = max(0, $this->stock_quantity - $qty);

        $this->update(['stock_quantity' => $newStock]);

        InventoryMovement::create([
            'menu_item_id' => $this->id,
            'warehouse_id' => $warehouseId,
            'type'         => 'WASTE',
            'quantity'     => $qty,
            'cause'        => $cause,
            'reference_id' => null,
            'created_by'   => $userId ?? auth()->id(),
        ]);
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class, 'menu_item_id');
    }

    public function receive(PurchaseOrder $po)
    {
        foreach ($po->items as $row) {
            if ($row->menu_item_id) {
                // increase directly on MenuItem
                $row->item->increaseStock(
                    qty: $row->quantity,
                    warehouseId: $po->warehouse_id,
                    cause: "PO #{$po->code}",
                    userId: auth()->id()
                );
            }
        }

        $po->update([
            'status'      => 'received',
            'approved_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.purchase-orders.show', $po)
            ->with('success', 'Goods received successfully.');
    }
}

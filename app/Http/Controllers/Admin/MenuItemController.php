<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItemRecipe;
use App\Http\Requests\Menu\StoreMenuItemRequest;
use App\Http\Requests\Menu\UpdateMenuItemRequest;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItemController extends Controller
{
    public function index(Request $request)
    {
        $base = MenuItem::query()
            ->with('category')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('sku', 'like', "%{$s}%")
                        ->orWhere('slug', 'like', "%{$s}%");
                });
            })
            ->when($request->filled('category_id'), fn($q) => $q->where('menu_category_id', $request->integer('category_id')));

        $totalActive   = (clone $base)->where('is_active', true)->count();
        $totalInactive = (clone $base)->where('is_active', false)->count();

        $status = $request->get('status', 'all'); // all|active|inactive
        $list = (clone $base)
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->orderByRaw('created_at DESC')
            ->orderBy('sort_order')
            ->orderBy('name');

        $items = $list->paginate(20)->withQueryString();
        $categories = MenuCategory::ordered()->get();

        if ($request->ajax()) {
            return view('admin.menu_items.partials.table', compact('items'))->render();
        }

        return view('admin.menu_items.index', compact('items', 'categories', 'status', 'totalActive', 'totalInactive'));
    }

    public function create()
    {
        $categories = MenuCategory::ordered()->get();
        return view('admin.menu_items.create', compact('categories'));
    }


    public function store(StoreMenuItemRequest $request)
    {
        // Map category field if needed
        $request->merge([
            'menu_category_id' => $request->input('menu_category_id', $request->input('category_id')),
        ]);

        $data = $request->validated();

        // Normalize
        $data['slug']         = $this->makeUniqueSlug($data['slug'] ?: $data['name']);
        $data['currency']     = strtoupper($data['currency'] ?? 'AED');
        $data['is_active']    = $request->boolean('is_active');
        $data['is_available'] = $request->boolean('is_available');
        $data['is_featured']  = $request->boolean('is_featured');
        $data['sort_order']   = (int)($data['sort_order'] ?? 0);
        $data['price']        = (float)($data['price'] ?? 0);
        $data['tags']         = filled($request->tags) ? array_values(array_filter(array_map('trim', explode(',', $request->tags)))) : null;
        $data['allergens']    = filled($request->allergens) ? array_values(array_filter(array_map('trim', explode(',', $request->allergens)))) : null;
        $data['sku']          = isset($data['sku']) ? preg_replace('/\s+/', '', $data['sku']) : null; // normalize "DRK - EC -001" -> "DRK-EC-001"
        $data['created_by']   = Auth::id();
        $data['updated_by']   = Auth::id();

        // Image
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu_items', 'public');
        }

        $item = MenuItem::create($data);
        $this->syncOptions($item, $request->input('options'));

        return redirect()->route('admin.menu-items.index')
            ->with('success', __('Item created successfully.'));
    }



    public function show(MenuItem $menu_item)
    {
        // Eager-load relations for the details page
        $menu_item->load(['creator', 'updater', 'category', 'options.values']);

        return view('admin.menu_items.show', ['item' => $menu_item]);
    }

    public function edit(MenuItem $menu_item)
    {
        $menu_item->load(['category', 'options.values']);
        $categories = MenuCategory::ordered()->get();
        return view('admin.menu_items.edit', ['item' => $menu_item, 'categories' => $categories]);
    }

    public function update(UpdateMenuItemRequest $request, MenuItem $menu_item)
    {
        $data = $request->validated();

        $data['slug']         = $data['slug'] ?: Str::slug($data['name']);
        $data['currency']     = strtoupper($data['currency'] ?? $menu_item->currency ?? 'AED');
        $data['is_active']    = $request->boolean('is_active');
        $data['is_available'] = $request->boolean('is_available');
        $data['is_featured']  = $request->boolean('is_featured');
        $data['sort_order']   = (int)($data['sort_order'] ?? ($menu_item->sort_order ?? 0));
        $data['price']        = isset($data['price']) ? (float)$data['price'] : $menu_item->price;

        $data['tags']      = filled($request->tags) ? array_values(array_filter(array_map('trim', explode(',', $request->tags)))) : null;
        $data['allergens'] = filled($request->allergens) ? array_values(array_filter(array_map('trim', explode(',', $request->allergens)))) : null;

        $data['updated_by'] = Auth::id();

        // Remove current image if requested
        if ($request->boolean('remove_image') && $menu_item->image_path) {
            Storage::disk('public')->delete($menu_item->image_path);
            $data['image_path'] = null;
        }

        // Replace with new image if uploaded
        if ($request->hasFile('image')) {
            if ($menu_item->image_path) {
                Storage::disk('public')->delete($menu_item->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menu_items', 'public');
        }

        $menu_item->update($data);

        // ⬇️ NEW: sync nested options/values
        $this->syncOptions($menu_item, $request->input('options'));

        return redirect()->route('admin.menu-items.index')
            ->with('success', __('Item updated successfully.'));
    }

    /**
     * Upsert options & values from a nested array payload.
     * - Keeps existing by id
     * - Creates new if id missing
     * - You can later add deletion logic if desired
     */
    private function syncOptions(MenuItem $item, ?array $options): void
    {
        if (!$options) return;

        $existingOptions = $item->options()->get()->keyBy('id');

        foreach ($options as $optData) {
            // Upsert option
            if (!empty($optData['id']) && $existingOptions->has($optData['id'])) {
                $option = $existingOptions->get($optData['id']);
                $option->fill([
                    'name'        => $optData['name'] ?? $option->name,
                    'type'        => in_array(($optData['type'] ?? 'single'), ['single', 'multiple'], true) ? $optData['type'] : 'single',
                    'is_required' => (bool)($optData['is_required'] ?? $option->is_required),
                    'min_choices' => $optData['min_choices'] ?? $option->min_choices,
                    'max_choices' => $optData['max_choices'] ?? $option->max_choices,
                    'sort_order'  => $optData['sort_order'] ?? $option->sort_order,
                    'is_active'   => (bool)($optData['is_active'] ?? $option->is_active),
                ])->save();
            } else {
                $option = $item->options()->create([
                    'name'        => $optData['name'] ?? 'Option',
                    'type'        => in_array(($optData['type'] ?? 'single'), ['single', 'multiple'], true) ? $optData['type'] : 'single',
                    'is_required' => (bool)($optData['is_required'] ?? false),
                    'min_choices' => $optData['min_choices'] ?? 0,
                    'max_choices' => $optData['max_choices'] ?? null,
                    'sort_order'  => $optData['sort_order'] ?? 0,
                    'is_active'   => (bool)($optData['is_active'] ?? true),
                ]);
            }

            // Upsert values
            $existingValues = $option->values()->get()->keyBy('id');
            foreach (($optData['values'] ?? []) as $valData) {
                if (!empty($valData['id']) && $existingValues->has($valData['id'])) {
                    $val = $existingValues->get($valData['id']);
                    $val->fill([
                        'label'      => $valData['label'] ?? $val->label,
                        'price_delta' => $valData['price_delta'] ?? $val->price_delta,
                        'is_default' => (bool)($valData['is_default'] ?? $val->is_default),
                        'sort_order' => $valData['sort_order'] ?? $val->sort_order,
                        'is_active'  => (bool)($valData['is_active'] ?? $val->is_active),
                    ])->save();
                } else {
                    $option->values()->create([
                        'label'      => $valData['label'] ?? 'Value',
                        'price_delta' => $valData['price_delta'] ?? 0,
                        'is_default' => (bool)($valData['is_default'] ?? false),
                        'sort_order' => $valData['sort_order'] ?? 0,
                        'is_active'  => (bool)($valData['is_active'] ?? true),
                    ]);
                }
            }
        }
    }


    public function destroy(MenuItem $menu_item)
    {
        // delete image file if exists
        if ($menu_item->image_path) {
            Storage::disk('public')->delete($menu_item->image_path);
        }

        $menu_item->delete();

        return redirect()->route('admin.menu-items.index')
            ->with('success', __('Item deleted successfully.'));
    }

    private function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        $original = $slug;
        $i = 2;

        // if you use SoftDeletes, include withTrashed() so we avoid collisions on the DB unique index
        $query = \App\Models\MenuItem::query(); // ->withTrashed() if model uses SoftDeletes

        while ($query->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)->exists()
        ) {
            $slug = "{$original}-{$i}";
            $i++;
        }
        return $slug;
    }

    public function recipe(MenuItem $item)
    {
        return view('admin.menu-items.recipe', [
            'item' => $item,
            'ingredients' => $item->recipe,
            'units' => ['unit', 'g', 'kg', 'ml', 'liter', 'pack']
        ]);
    }

    public function recipeStore(MenuItem $item, Request $request)
    {
        $request->validate([
            'ingredient_name' => 'required|string',
            'quantity'        => 'required|numeric|min:0.001',
            'unit_type'       => 'required|string',
        ]);

        $item->recipe()->create([
            'ingredient_name' => $request->ingredient_name,
            'quantity'        => $request->quantity,
            'unit_type'       => $request->unit_type,
            'sort_order'      => $item->recipe()->max('sort_order') + 1,
        ]);

        return back()->with('success', 'Ingredient added.');
    }

    public function recipeDelete(MenuItem $item, MenuItemRecipe $recipe)
    {
        $recipe->delete();
        return back()->with('success', 'Ingredient removed.');
    }
}

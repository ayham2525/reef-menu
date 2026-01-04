<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class ReefMenuController extends Controller
{
    public function __invoke(Request $request)
    {
        // 1) Base query (include image columns & eager-load primary image + category)
        $base = MenuItem::query()
            ->with([
                'category:id,name,slug',
                'primaryImage:id,menu_item_id,disk,path,is_primary,sort_order',
            ])
            ->ordered()
            ->select([
                'id',
                'menu_category_id',
                'name',
                'slug',
                'description',
                'price',
                'currency',
                'image_path',   // needed for primary_image_url fallback
                'image_disk',   // needed for primary_image_url fallback
            ]);

        // 2) Strict view: active + available
        $strict = (clone $base)->active()->available();
        $items  = $strict->get();

        // 3) Fallback to "show everything" if strict returns nothing (for dev/seeded data)
        $fallbackUsed = false;
        if ($items->isEmpty()) {
            $items = (clone $base)->get();
            $fallbackUsed = true;
        }

        // 4) Optional debug: /reef-menu?debug=1
        if ($request->boolean('debug')) {
            $counts = [
                'total_items'       => MenuItem::query()->count(),
                'active_only'       => MenuItem::query()->active()->count(),
                'available_only'    => MenuItem::query()->available()->count(),
                'active_available'  => MenuItem::query()->active()->available()->count(),
                'fallback_used'     => $fallbackUsed,
            ];
            dump($counts);
        }

        // 5) Build categories from the items being shown
        $categories = $items->pluck('category')->filter()->unique('slug')->values()
            ->map(fn($c) => ['slug' => $c->slug, 'name' => $c->name]);

        // 6) Shape items for the blade/JS
        $itemsForJs = $items->map(function (MenuItem $m) {
            return [
                'id'             => (string) $m->id,
                'name'           => $m->name,
                'category'       => optional($m->category)->slug ?? 'other',
                'category_label' => optional($m->category)->name ?? 'Other',
                'price'          => (float) $m->price,
                'currency'       => $m->currency ?: 'AED',
                'img'            => $m->primary_image_url, // now resolves (eager-loaded / fallback)
                'desc'           => $m->description,
            ];
        });

        return view('reef-menu', [
            'items'      => $itemsForJs,
            'categories' => $categories,
            'vatRate'    => 0.05,
            'currency'   => 'AED',
        ]);
    }
}

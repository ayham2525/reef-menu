<?php
// app/Http/Controllers/Admin/MenuCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\StoreMenuCategoryRequest;
use App\Http\Requests\Menu\UpdateMenuCategoryRequest;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $base = MenuCategory::query()
            ->with('parent')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('code', 'like', "%{$s}%")
                        ->orWhere('slug', 'like', "%{$s}%");
                });
            });

        $totalActive   = (clone $base)->where('is_active', true)->count();
        $totalInactive = (clone $base)->where('is_active', false)->count();

        $status = $request->get('status', 'all'); // all|active|inactive
        $list = (clone $base)
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        $categories = $list->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('admin.menu_categories.partials.table', compact('categories'))->render();
        }

        return view('admin.menu_categories.index', compact('categories', 'status', 'totalActive', 'totalInactive'));
    }

    public function create()
    {
        $parents = MenuCategory::ordered()->get();
        return view('admin.menu_categories.create', compact('parents'));
    }

    public function store(StoreMenuCategoryRequest $request)
    {
        $data = $request->validated();

        // Normalize
        $data['code']       = $data['code'] ?? null;
        $data['slug']       = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int)($data['sort_order'] ?? 0);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        MenuCategory::create($data);

        return redirect()->route('admin.menu-categories.index')
            ->with('success', __('Category created successfully.'));
    }

    public function show(MenuCategory $menu_category)
    {
        $menu_category->load(['parent', 'children', 'items']);
        return view('admin.menu_categories.show', ['category' => $menu_category]);
    }

    public function edit(MenuCategory $menu_category)
    {
        $parents = MenuCategory::where('id', '!=', $menu_category->id)->ordered()->get();
        return view('admin.menu_categories.edit', ['category' => $menu_category, 'parents' => $parents]);
    }

    public function update(UpdateMenuCategoryRequest $request, MenuCategory $menu_category)
    {
        $data = $request->validated();

        // Prevent self-parenting
        if (!empty($data['parent_id']) && (int)$data['parent_id'] === (int)$menu_category->id) {
            unset($data['parent_id']);
        }

        // Normalize
        $data['code']       = $data['code'] ?? null;
        $data['slug']       = $data['slug'] ?: Str::slug($data['name']);
        $data['is_active']  = $request->boolean('is_active');
        $data['sort_order'] = (int)($data['sort_order'] ?? ($menu_category->sort_order ?? 0));
        $data['updated_by'] = Auth::id();

        $menu_category->update($data);

        return redirect()->route('admin.menu-categories.index')
            ->with('success', __('Category updated successfully.'));
    }

    public function destroy(MenuCategory $menu_category)
    {
        $menu_category->delete();
        return redirect()->route('admin.menu-categories.index')
            ->with('success', __('Category deleted successfully.'));
    }
}

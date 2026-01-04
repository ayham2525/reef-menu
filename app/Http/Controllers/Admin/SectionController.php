<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Section\StoreSectionRequest;
use App\Http\Requests\Section\UpdateSectionRequest;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        // Base query with optional search
        $base = Section::query()
            ->with('parent')
            ->when($request->filled('search'), function ($query) use ($request) {
                $s = $request->string('search')->toString();
                $query->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('code', 'like', "%{$s}%")
                        ->orWhere('slug', 'like', "%{$s}%");
                });
            });

        // Counts by status (respecting search)
        $totalActive   = (clone $base)->where('is_active', true)->count();
        $totalInactive = (clone $base)->where('is_active', false)->count();

        // Filter list by status
        $status = $request->get('status', 'all'); // 'all' | 'active' | 'inactive'
        $list = (clone $base)
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');

        $sections = $list->paginate(20)->withQueryString();

        if ($request->ajax()) {
            // For AJAX calls, return only the table partial
            return view('admin.sections.partials.table', compact('sections'))->render();
        }

        return view('admin.sections.index', compact('sections', 'status', 'totalActive', 'totalInactive'));
    }

    public function create()
    {
        $parents = Section::orderBy('name')->get();
        return view('admin.sections.create', compact('parents'));
    }

    public function store(StoreSectionRequest $request)
    {
        $data = $request->validated();

        // Normalize optional fields to null
        $data['code'] = $data['code'] ?? null;
        $data['slug'] = $data['slug'] ?: null;

        // Auto-generate slug from name if missing
        if (blank($data['slug']) && !blank($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Defaults
        $data['is_active']  = array_key_exists('is_active', $data) ? (int) $data['is_active'] : 1;
        $data['sort_order'] = array_key_exists('sort_order', $data) ? (int) $data['sort_order'] : 0;

        // Audit
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Section::create($data);

        return redirect()->route('admin.sections.index')
            ->with('success', __('Section created successfully.'));
    }

    public function show(Section $section)
    {
        $section->load('children');
        return view('admin.sections.show', compact('section'));
    }

    public function edit(Section $section)
    {
        $parents = Section::where('id', '!=', $section->id)->orderBy('name')->get();
        return view('admin.sections.edit', compact('section', 'parents'));
    }

    public function update(UpdateSectionRequest $request, Section $section)
    {
        $data = $request->validated();

        // Hard guard: prevent self-parenting even if it slips past validation
        if (!empty($data['parent_id']) && (int) $data['parent_id'] === (int) $section->id) {
            unset($data['parent_id']);
        }

        // Normalize optional fields to null
        $data['code'] = $data['code'] ?? null;
        $data['slug'] = $data['slug'] ?: null;

        // Auto-generate slug if cleared
        if (blank($data['slug']) && !blank($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Defaults (keep existing if not provided)
        if (!array_key_exists('is_active', $data)) {
            $data['is_active'] = (int) $section->is_active;
        } else {
            $data['is_active'] = (int) $data['is_active'];
        }

        if (!array_key_exists('sort_order', $data)) {
            $data['sort_order'] = (int) ($section->sort_order ?? 0);
        } else {
            $data['sort_order'] = (int) $data['sort_order'];
        }

        // Audit
        $data['updated_by'] = Auth::id();

        $section->update($data);

        return redirect()->route('admin.sections.index')
            ->with('success', __('Section updated successfully.'));
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('admin.sections.index')
            ->with('success', __('Section deleted successfully.'));
    }
}

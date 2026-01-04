<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{

    public function index(Request $request)
    {
        // Base query with search
        $base = Position::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $s = $request->string('search')->toString();
                $query->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('code', 'like', "%{$s}%")
                        ->orWhere('slug', 'like', "%{$s}%");
                });
            });

        // Counts (respecting search)
        $totalActive   = (clone $base)->where('is_active', true)->count();
        $totalInactive = (clone $base)->where('is_active', false)->count();

        // Status filter
        $status = $request->get('status', 'all'); // all | active | inactive
        $list = (clone $base)
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->orderBy('sort_order')
            ->orderBy('name');

        $positions = $list->paginate(20)->withQueryString();

        // For AJAX requests return ONLY the table partial HTML
        if ($request->ajax()) {
            return view('admin.positions.partials.table', compact('positions'))->render();
        }

        // First full page load
        return view('admin.positions.index', compact('positions', 'status', 'totalActive', 'totalInactive'));
    }


    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(StorePositionRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        Position::create($data);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        return view('admin.positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    public function update(UpdatePositionRequest $request, Position $position)
    {
        $data = $request->validated();
        $data['updated_by'] = Auth::id();
        $data['is_active'] = $request->boolean('is_active');

        $position->update($data);

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('admin.positions.index')
            ->with('success', 'Position deleted successfully.');
    }
}

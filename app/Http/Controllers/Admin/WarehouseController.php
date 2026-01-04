<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::query()->orderBy('name');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        $warehouses = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.warehouses.partials.table', compact('warehouses'))->render();
        }

        return view('admin.warehouses.index', compact('warehouses'));
    }


    public function create()
    {
        return view('admin.warehouses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'nullable|string|max:50',
            'location'  => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        Warehouse::create($request->all());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'code'      => 'nullable|string|max:50',
            'location'  => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $warehouse->update($request->all());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return back()->with('success', 'Warehouse deleted.');
    }
}

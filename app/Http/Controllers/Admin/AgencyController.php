<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agency\StoreAgencyRequest;
use App\Http\Requests\Agency\UpdateAgencyRequest;
use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    /**
     * Display a listing of agencies with filters (status, search) and AJAX pagination.
     */
    public function index(Request $request)
    {
        $status = $request->string('status')->toString() ?: 'all';

        $query = Agency::query()
            ->withCount('brokers') // <-- add this
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(fn($qq) => $qq
                    ->where('name', 'like', "%$s%")
                    ->orWhere('code', 'like', "%$s%")
                    ->orWhere('license_no', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('phone', 'like', "%$s%"));
            })
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest();

        $agencies = $query->paginate(10)->withQueryString();

        $totalActive   = Agency::where('is_active', true)->count();
        $totalInactive = Agency::where('is_active', false)->count();

        // AJAX partial rendering
        if ($request->ajax()) {
            return view('admin.agencies.partials.table', compact('agencies'))->render();
        }

        return view('admin.agencies.index', [
            'agencies'      => $agencies,
            'status'        => $status,
            'totalActive'   => $totalActive,
            'totalInactive' => $totalInactive,
        ]);
    }

    /**
     * Show the form for creating a new agency.
     */
    public function create()
    {
        return view('admin.agencies.create');
    }

    /**
     * Store a newly created agency.
     */
    public function store(StoreAgencyRequest $request)
    {
        Agency::create($request->validated());

        return redirect()
            ->route('admin.agencies.index')
            ->with('success', __('Agency created successfully.'));
    }

    /**
     * Show the form for editing the specified agency.
     */
    public function edit(Agency $agency)
    {
        return view('admin.agencies.edit', compact('agency'));
    }

    /**
     * Update the specified agency.
     */
    public function update(UpdateAgencyRequest $request, Agency $agency)
    {
        $agency->update($request->validated());

        return redirect()
            ->route('admin.agencies.index')
            ->with('success', __('Agency updated successfully.'));
    }

    /**
     * Display the specified agency details.
     */
    public function show(Agency $agency)
    {
        return view('admin.agencies.show', compact('agency'));
    }

    /**
     * Remove the specified agency.
     */
    public function destroy(Agency $agency)
    {
        $agency->delete();

        return redirect()
            ->route('admin.agencies.index')
            ->with('success', __('Agency deleted successfully.'));
    }
}

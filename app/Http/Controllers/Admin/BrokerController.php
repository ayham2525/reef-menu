<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Broker\StoreBrokerRequest;
use App\Http\Requests\Broker\UpdateBrokerRequest;
use App\Models\Broker;
use App\Models\Agency;
use Illuminate\Http\Request;

class BrokerController extends Controller
{
    /**
     * List brokers with filters (status, search, agency) and AJAX pagination.
     */
    public function index(Request $request)
    {
        $status   = $request->string('status')->toString() ?: 'all'; // all|active|inactive
        $agencyId = $request->string('agency_id')->toString() ?: null; // UUID, not integer!

        $query = Broker::query()
            ->with('agency')
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search')->toString();
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%")
                        ->orWhere('brn', 'like', "%{$s}%");
                });
            })
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest();

        $brokers = $query->paginate(10)->withQueryString();

        // Stats respect the selected agency
        $totalActive = Broker::where('is_active', true)
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->count();

        $totalInactive = Broker::where('is_active', false)
            ->when($agencyId, fn($q) => $q->where('agency_id', $agencyId))
            ->count();

        // Filters dropdown
        $agencies = Agency::orderBy('name')->get(['id', 'name']);

        if ($request->ajax()) {
            return view('admin.brokers.partials.table', compact('brokers'))->render();
        }

        return view('admin.brokers.index', [
            'brokers'         => $brokers,
            'agencies'        => $agencies,
            'status'          => $status,
            'selectedAgency'  => $agencyId, // UUID string
            'totalActive'     => $totalActive,
            'totalInactive'   => $totalInactive,
        ]);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $agencies = Agency::orderBy('name')->get(['id', 'name']);
        return view('admin.brokers.create', compact('agencies'));
    }

    /**
     * Store broker.
     */
    public function store(StoreBrokerRequest $request)
    {
        Broker::create($request->validated());

        return redirect()
            ->route('admin.brokers.index')
            ->with('success', __('Broker created successfully.'));
    }

    /**
     * Show edit form.
     */
    public function edit(Broker $broker)
    {
        $agencies = Agency::orderBy('name')->get(['id', 'name']);
        return view('admin.brokers.edit', compact('broker', 'agencies'));
    }

    /**
     * Update broker.
     */
    public function update(UpdateBrokerRequest $request, Broker $broker)
    {
        $data = $request->validated();

        // If the checkbox isn't sent, default to 0 (inactive)
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $broker->update($data);

        return redirect()
            ->route('admin.brokers.index')
            ->with('success', __('Broker updated successfully.'));
    }


    /**
     * Show broker details.
     */
    public function show(Broker $broker)
    {
        $broker->load('agency');
        return view('admin.brokers.show', compact('broker'));
    }

    /**
     * Delete broker.
     */
    public function destroy(Broker $broker)
    {
        $broker->delete();

        return redirect()
            ->route('admin.brokers.index')
            ->with('success', __('Broker deleted successfully.'));
    }
}

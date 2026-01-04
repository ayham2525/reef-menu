@extends('adminlte::page')

@section('title', 'Stock Movement Report')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-random text-primary"></i> Stock Movement Report
    </h1>

    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Inventory
    </a>
</div>
@stop


@section('content')

{{-- FILTERS --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h3 class="card-title mb-0">
            <i class="fas fa-filter text-muted"></i> Filters
        </h3>
    </div>

    <div class="card-body">
        <form class="mb-0" method="GET">

            <div class="form-row">

                {{-- Date From --}}
                <div class="col-md-3 mb-3">
                    <label>From Date</label>
                    <input type="date" class="form-control" name="from"
                        value="{{ request('from') }}">
                </div>

                {{-- Date To --}}
                <div class="col-md-3 mb-3">
                    <label>To Date</label>
                    <input type="date" class="form-control" name="to"
                        value="{{ request('to') }}">
                </div>

                {{-- Item --}}
                <div class="col-md-3 mb-3">
                    <label>Item</label>
                    <select name="menu_item_id" class="form-control">
                        <option value="">All Items</option>
                        @foreach($items as $i)
                            <option value="{{ $i->id }}"
                                {{ request('menu_item_id') == $i->id ? 'selected' : '' }}>
                                {{ $i->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Warehouse --}}
                <div class="col-md-3 mb-3">
                    <label>Warehouse</label>
                    <select name="warehouse_id" class="form-control">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}"
                                {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="form-row">

                {{-- Type --}}
                <div class="col-md-3 mb-3">
                    <label>Movement Type</label>
                    <select name="type" class="form-control">
                        <option value="">All</option>
                        <option value="IN" {{ request('type')=='IN'?'selected':'' }}>IN</option>
                        <option value="OUT" {{ request('type')=='OUT'?'selected':'' }}>OUT</option>
                        <option value="WASTE" {{ request('type')=='WASTE'?'selected':'' }}>WASTE</option>
                        <option value="ADJUSTMENT" {{ request('type')=='ADJUSTMENT'?'selected':'' }}>ADJUSTMENT</option>
                    </select>
                </div>

                {{-- User --}}
                <div class="col-md-3 mb-3">
                    <label>User</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}"
                                {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-3 text-right">
                <button class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>

                <a href="{{ route('admin.reports.stock-movement') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>

        </form>
    </div>
</div>


{{-- TABLE --}}
<div class="card shadow-sm">

    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-history text-muted"></i> Movement History
        </h3>

        <div>
            <button class="btn btn-success btn-sm">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>

            <button class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
        </div>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Warehouse</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Cause</th>
                        <th>User</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($movements as $m)
                        <tr>
                            <td>{{ ($movements->firstItem() ?? 1) + $loop->index }}</td>

                            <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>

                            <td><strong>{{ $m->item->name }}</strong></td>

                            <td>{{ $m->warehouse->name ?? 'Default' }}</td>

                            <td>
                                @if($m->type == 'IN')
                                    <span class="badge badge-success">IN</span>
                                @elseif($m->type == 'OUT')
                                    <span class="badge badge-warning">OUT</span>
                                @elseif($m->type == 'WASTE')
                                    <span class="badge badge-danger">WASTE</span>
                                @else
                                    <span class="badge badge-info">ADJUSTMENT</span>
                                @endif
                            </td>

                            <td>{{ number_format($m->quantity, 3) }}</td>

                            <td>{{ $m->cause ?? '-' }}</td>

                            <td>{{ $m->creator->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="p-3">
            {{ $movements->withQueryString()->links('pagination::bootstrap-4') }}
        </div>

    </div>
</div>

@stop

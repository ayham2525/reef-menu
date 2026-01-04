@extends('adminlte::page')

@section('title', 'Warehouse Transfer Report')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-exchange-alt text-primary"></i> Warehouse Transfer Report
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
                    <input type="date" class="form-control" name="date_from"
                        value="{{ request('date_from') }}">
                </div>

                {{-- Date To --}}
                <div class="col-md-3 mb-3">
                    <label>To Date</label>
                    <input type="date" class="form-control" name="date_to"
                        value="{{ request('date_to') }}">
                </div>

                {{-- From Warehouse --}}
                <div class="col-md-3 mb-3">
                    <label>From Warehouse</label>
                    <select name="from_warehouse_id" class="form-control">
                        <option value="">All</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}"
                                {{ request('from_warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- To Warehouse --}}
                <div class="col-md-3 mb-3">
                    <label>To Warehouse</label>
                    <select name="to_warehouse_id" class="form-control">
                        <option value="">All</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}"
                                {{ request('to_warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>


            <div class="form-row">

                {{-- Status --}}
                <div class="col-md-3 mb-3">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">All</option>
                        <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                        <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                    </select>
                </div>

            </div>


            <div class="mt-3 text-right">
                <button class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>

                <a href="{{ route('admin.reports.stock-transfers') }}" class="btn btn-secondary">
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
            <i class="fas fa-exchange-alt text-muted"></i> Transfer History
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
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Created By</th>
                        <th>Approved By</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($transfers as $t)
                        <tr>
                            <td>{{ ($transfers->firstItem() ?? 1) + $loop->index }}</td>

                            <td>{{ $t->created_at->format('Y-m-d H:i') }}</td>

                            <td><strong>{{ $t->fromWarehouse->name }}</strong></td>

                            <td><strong>{{ $t->toWarehouse->name }}</strong></td>

                            <td>
                                @if($t->status === 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @else
                                    <span class="badge badge-warning">Draft</span>
                                @endif
                            </td>

                            <td>
                                <ul class="pl-3 mb-0">
                                    @foreach($t->items as $i)
                                        <li>
                                            {{ $i->item->name }} —
                                            <strong>{{ $i->quantity }}</strong> {{ $i->unit_type }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td>{{ $t->creator->name ?? '-' }}</td>
                            <td>{{ $t->approver->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> No transfers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="p-3">
            {{ $transfers->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@stop

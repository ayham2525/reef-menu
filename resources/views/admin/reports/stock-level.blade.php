@extends('adminlte::page')

@section('title', 'Stock Level Report')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>
        <i class="fas fa-box-open text-primary"></i> Stock Level Report
    </h1>

    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Inventory
    </a>
</div>
@stop


@section('content')

{{-- Filters --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h3 class="card-title mb-0">
            <i class="fas fa-filter text-muted"></i> Filters
        </h3>
    </div>

    <div class="card-body">

        <form method="GET" class="mb-0">

            <div class="form-row">

                {{-- Search --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted">Item Name</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Search item..."
                           value="{{ request('search') }}">
                </div>

                {{-- Warehouse --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted">Warehouse</label>
                    <select name="warehouse_id" class="form-control">
                        <option value="">All Warehouses</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Low stock --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted d-block">Low Stock Only</label>
                    <label class="switch">
                        <input type="checkbox" name="low" value="1" {{ request('low') == "1" ? 'checked' : '' }}>
                        <span class="slider round"></span>
                    </label>
                </div>

            </div>

            <div class="mt-3 text-right">
                <button class="btn btn-primary">
                    <i class="fas fa-search"></i> Apply Filters
                </button>

                <a href="{{ route('admin.reports.stock-level') }}" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Reset
                </a>
            </div>

        </form>

    </div>
</div>


{{-- Report Table --}}
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-boxes text-muted"></i> Stock Levels
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
                        <th width="5%">#</th>
                        <th>Item</th>
                        <th>Warehouse</th>
                        <th width="15%">Quantity</th>
                        <th width="15%">Minimum</th>
                        <th width="12%">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($stocks as $s)
                        <tr class="{{ $s->quantity <= $s->min_quantity ? 'table-danger' : '' }}">

                            <td>{{ ($stocks->firstItem() ?? 1) + $loop->index }}</td>

                            <td>
                                <strong>{{ $s->item->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $s->item->sku }}</small>
                            </td>

                            <td>{{ $s->warehouse->name ?? 'Default' }}</td>

                            <td>
                                <strong>{{ number_format($s->quantity, 3) }}</strong>
                                <small class="text-muted">{{ $s->unit_type }}</small>
                            </td>

                            <td>
                                {{ number_format($s->min_quantity, 3) }}
                                <small class="text-muted">{{ $s->unit_type }}</small>
                            </td>

                            <td>
                                @if($s->quantity <= $s->min_quantity)
                                    <span class="badge badge-danger">
                                        <i class="fas fa-exclamation-circle"></i> Low
                                    </span>
                                @else
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> OK
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle"></i> No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="p-3">
            {{ $stocks->withQueryString()->links('pagination::bootstrap-4') }}
        </div>

    </div>
</div>

@stop


@section('css')
<style>
/* Toggle Switch */
.switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}
.switch input { display:none; }

.slider {
    position: absolute;
    cursor: pointer;
    background-color: #ccc;
    border-radius: 34px;
    transition: .4s;
    top: 0; left: 0; right: 0; bottom: 0;
}
.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: .4s;
}
input:checked + .slider {
    background-color: #28a745;
}
input:checked + .slider:before {
    transform: translateX(24px);
}
.slider.round { border-radius: 34px; }
.slider.round:before { border-radius: 50%; }
</style>
@stop

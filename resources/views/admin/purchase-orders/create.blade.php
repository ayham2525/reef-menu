@extends('adminlte::page')

@section('title', __('Create Purchase Order'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-file-invoice-dollar text-primary"></i> {{ __('New Purchase Order') }}
        </h1>

        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back to Purchase Orders') }}
        </a>
    </div>
@stop

@section('content')

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            <strong>{{ __('Please fix the errors below:') }}</strong>
            <ul class="mt-2 pl-4 mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm">
        <form action="{{ route('admin.purchase-orders.store') }}" method="POST" autocomplete="off">
            @csrf

            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fa fa-info-circle text-muted"></i> {{ __('PO Details') }}
                </h3>
            </div>

            <div class="card-body">

                {{-- Vendor & Warehouse --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fa fa-truck-loading text-muted"></i> Vendor <span class="text-danger">*</span>
                        </label>
                        <select name="vendor_id" class="form-control" required>
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            <i class="fa fa-warehouse text-muted"></i> Warehouse <span class="text-danger">*</span>
                        </label>
                        <select name="warehouse_id" class="form-control" required>
                            <option value="">Select Warehouse</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>

                {{-- ITEMS TABLE --}}
                <h5 class="mb-2"><i class="fa fa-box"></i> {{ __('Items') }}</h5>

                <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                        <thead class="thead-light">
                            <tr>
                                <th width="40%">Item</th>
                                <th width="12%">Qty</th>
                                <th width="15%">Unit Price</th>
                                <th width="15%">Unit Type</th>
                                <th width="8%"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-secondary custom-btn mb-3" onclick="addRow()">
                    <i class="fa fa-plus"></i> Add Row
                </button>

            </div>

            {{-- FOOTER --}}
            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn-primary custom-btn">
                    <i class="fa fa-save mr-1"></i> Save Purchase Order
                </button>
            </div>

        </form>
    </div>

@stop

@section('js')
<script>
let index = 0;

function addRow() {
    let row = `
        <tr>
            <td>
                <select name="items[${index}][menu_item_id]" class="form-control">
                    <option value="">Custom Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>

                <input type="text"
                       name="items[${index}][item_name]"
                       class="form-control mt-1"
                       placeholder="Item Name (if custom)">
            </td>

            <td>
                <input type="number"
                       step="0.001"
                       name="items[${index}][quantity]"
                       class="form-control"
                       required>
            </td>

            <td>
                <input type="number"
                       step="0.01"
                       name="items[${index}][unit_price]"
                       class="form-control"
                       required>
            </td>

            <td>
                <input type="text"
                       name="items[${index}][unit_type]"
                       class="form-control"
                       placeholder="kg / pcs"
                       required>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    document.querySelector('#items-table tbody')
        .insertAdjacentHTML('beforeend', row);

    index++;
}

function removeRow(btn) {
    btn.closest('tr').remove();
}
</script>
@stop

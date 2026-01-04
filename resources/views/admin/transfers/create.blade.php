@extends('adminlte::page')

@section('title', 'Create Transfer')

@section('content_header')
    <h1><i class="fa fa-plus text-primary"></i> New Warehouse Transfer</h1>
@stop

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Please fix the errors:</strong>
    <ul class="mt-2 mb-0">
        @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-body">

        <form action="{{ route('admin.inventory.transfers.store') }}" method="POST">
            @csrf

            <div class="row mb-4">

                <div class="col-md-6">
                    <label>From Warehouse</label>
                    <select name="from_warehouse_id" class="form-control" required>
                        <option value="">Select...</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label>To Warehouse</label>
                    <select name="to_warehouse_id" class="form-control" required>
                        <option value="">Select...</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <hr>

            <h4>Items</h4>

            <table class="table table-bordered" id="itemsTable">
                <thead class="thead-light">
                    <tr>
                        <th width="40%">Item</th>
                        <th width="20%">Qty</th>
                        <th width="20%">Unit Type</th>
                        <th width="5%"></th>
                    </tr>
                </thead>

                <tbody></tbody>
            </table>

            <button type="button" class="btn btn-secondary" id="addRowBtn">
                <i class="fa fa-plus"></i> Add Item
            </button>

            <div class="mt-4 text-right">
                <button class="btn btn-primary">
                    <i class="fa fa-check"></i> Save Transfer
                </button>
            </div>

        </form>

    </div>
</div>

@stop

@section('js')
<script>
let items = @json($items);
let index = 0;

function addRow() {
    let row = `
        <tr>
            <td>
                <select name="items[${index}][menu_item_id]" class="form-control" required>
                    <option value="">Select item...</option>
                    ${items.map(i => `<option value="${i.id}">${i.name}</option>`).join('')}
                </select>
            </td>

            <td>
                <input type="number" step="0.001"
                       name="items[${index}][quantity]"
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
                <button type="button" class="btn btn-danger btn-sm removeRow">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>
    `;

    $('#itemsTable tbody').append(row);
    index++;
}

$('#addRowBtn').on('click', addRow);

$(document).on('click', '.removeRow', function () {
    $(this).closest('tr').remove();
});

// Add first row automatically
addRow();
</script>
@stop

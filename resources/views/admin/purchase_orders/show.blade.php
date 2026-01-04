@extends('adminlte::page')

@section('title', 'Purchase Order Details')

@section('content_header')
    <h1>Purchase Order {{ $po->code }}</h1>
@endsection

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <p><strong>Vendor:</strong> {{ optional($po->vendor)->name }}</p>
        <p><strong>Warehouse:</strong> {{ optional($po->warehouse)->name }}</p>
        <p><strong>Status:</strong>
            <span class="badge
                {{ $po->status == 'received' ? 'bg-success' : 'bg-warning' }}">
                {{ ucfirst($po->status) }}
            </span>
        </p>

        <hr>

        <h5>Items</h5>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Type</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($po->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->unit_type }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h4 class="text-end">Total: {{ number_format($po->total_amount, 2) }}</h4>

        @if($po->status !== 'received')
        <form method="POST"
              action="{{ route('admin.purchase-orders.receive', $po->id) }}"
              class="text-end mt-3">
            @csrf
            <button type="submit" class="btn btn-success"
                    onclick="return confirm('Receive all items into warehouse?')">
                <i class="fas fa-check"></i> Receive Goods
            </button>
        </form>
        @endif

    </div>
</div>

@endsection

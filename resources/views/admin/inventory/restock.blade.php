@extends('adminlte::page')

@section('title', 'Restock: ' . $stock->item->name)

@section('content')

<div class="card shadow-sm">
    <div class="card-body">

        <h4 class="mb-3">Restock Item</h4>

        <p class="text-muted mb-4">
            <strong>Item:</strong> {{ $stock->item->name }} <br>
            <strong>Warehouse:</strong> {{ $stock->warehouse->name ?? 'Default' }} <br>
            <strong>Current Stock:</strong> {{ $stock->quantity }} {{ $stock->unit_type }}
        </p>

        <form action="{{ route('admin.inventory.restock', $stock->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Quantity to Add</label>
                <input type="number" step="0.001" name="quantity"
                       class="form-control @error('quantity') is-invalid @enderror"
                       required>

                @error('quantity')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Cause / Note (optional)</label>
                <input type="text" name="cause" class="form-control">
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.inventory.show', $stock->id) }}" class="btn btn-secondary">
                    Back
                </a>

                <button class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Add Stock
                </button>
            </div>
        </form>

    </div>
</div>

@endsection

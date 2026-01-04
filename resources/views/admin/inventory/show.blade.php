@extends('adminlte::page')

@section('title', __('Inventory Details'))

@section('content')

<div class="card shadow-sm mb-4">
    <div class="card-body">

        <h4>{{ $stock->item->name }}</h4>

        <p class="text-muted">
            <strong>{{ __('Warehouse') }}:</strong> {{ $stock->warehouse->name ?? 'Default' }} <br>
            <strong>{{ __('Current Stock') }}:</strong> {{ $stock->quantity }} {{ $stock->unit_type }} <br>
            <strong>{{ __('Minimum Required') }}:</strong> {{ $stock->min_quantity }}
        </p>

        <a href="{{ route('admin.inventory.restock.form', $stock->id) }}" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> {{ __('Restock') }}
        </a>

        <a href="{{ route('admin.inventory.adjust.form', $stock->id) }}" class="btn btn-danger btn-sm">
            <i class="fa fa-minus"></i> {{ __('Adjust / Waste') }}
        </a>

    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <h5>{{ __('Movement History') }}</h5>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th>{{ __('Qty') }}</th>
                    <th>{{ __('Cause') }}</th>
                    <th>{{ __('User') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                    <td><strong>{{ $m->type }}</strong></td>
                    <td>{{ $m->quantity }}</td>
                    <td>{{ $m->cause ?: '-' }}</td>
                    <td>{{ $m->creator->name ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $movements->links() }}

    </div>
</div>

@endsection

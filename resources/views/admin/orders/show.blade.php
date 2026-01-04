@extends('adminlte::page')

@section('title', __('Order Details'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-receipt text-primary"></i> {{ __('Order Details') }}
        </h1>

        <div class="orders_buttons">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mr-2">
                <i class="fa fa-list mr-1"></i> {{ __('View Orders') }}
            </a>

            <a href="{{ route('admin.orders.create') }}" class="btn btn-primary custom-btn create_new_order">
                <i class="fa fa-plus mr-1"></i> {{ __('Create New Order') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Order Code:') }} {{ $order->code }}</h5>
        </div>

        <div class="card-body">
            <p><strong>{{ __('Status:') }}</strong> {{ ucfirst($order->status) }}</p>

            <p>
                <strong>{{ __('Agency Name:') }}</strong>
                {{ $order->agency_name ? $order->agency_name : '—' }}
            </p>

            <p><strong>{{ __('Total:') }}</strong> {{ number_format($order->total_amount, 2) }}</p>
            <p><strong>{{ __('Notes:') }}</strong> {{ $order->notes }}</p>
            <p><strong>{{ __('Placed At:') }}</strong> {{ $order->placed_at }}</p>

            <hr>

            <h5 class="mb-3"><i class="fa fa-box mr-1"></i> {{ __('Order Items') }}</h5>

            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ __('Item Name') }}</th>
                            <th>{{ __('Quantity') }}</th>
                            <th>{{ __('Unit Price') }}</th>
                            <th>{{ __('Line Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@stop

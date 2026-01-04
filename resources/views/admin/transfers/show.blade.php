@extends('adminlte::page')

@section('title', "Transfer #$transfer->id")

@section('content_header')
    <h1>
        <i class="fa fa-exchange-alt text-primary"></i>
        Transfer #{{ $transfer->id }}
    </h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">

        <h4>Transfer Details</h4>
        <p>
            <strong>From:</strong> {{ $transfer->fromWarehouse->name }} <br>
            <strong>To:</strong> {{ $transfer->toWarehouse->name }} <br>
            <strong>Status:</strong>
                @if($transfer->status == 'approved')
                    <span class="badge badge-success">Approved</span>
                @else
                    <span class="badge badge-warning">Draft</span>
                @endif
        </p>

        <hr>

        <h5>Items</h5>
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Item</th>
                    <th width="20%">Qty</th>
                    <th width="20%">Unit</th>
                </tr>
            </thead>

            <tbody>
            @foreach($transfer->items as $row)
                <tr>
                    <td>{{ $row->item->name }}</td>
                    <td>{{ $row->quantity }}</td>
                    <td>{{ $row->unit_type }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        @if($transfer->status != 'approved')
        <form action="{{ route('admin.transfers.approve', $transfer->id) }}" method="POST" class="text-right mt-4">
            @csrf

            <button class="btn btn-success"
                onclick="return confirm('Approve this transfer and move stock?');">
                <i class="fa fa-check"></i> Approve Transfer
            </button>
        </form>
        @endif

    </div>
</div>

@stop

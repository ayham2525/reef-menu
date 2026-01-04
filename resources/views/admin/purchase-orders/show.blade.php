@extends('adminlte::page')

@section('title', "PO $po->code")

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fas fa-file-invoice-dollar text-primary"></i>
        {{ __('Purchase Order') }} <span class="text-muted">#{{ $po->code }}</span>
    </h1>

    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left"></i> {{ __('Back to List') }}
    </a>
</div>
@stop

@section('content')

{{-- Success --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

<div class="card shadow-sm">

    <div class="card-header bg-light">
        <h3 class="card-title mb-0">
            <i class="fa fa-info-circle text-muted mr-1"></i> {{ __('PO Details') }}
        </h3>
    </div>

    <div class="card-body">

        {{-- Header Details --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <label class="text-muted">{{ __('Vendor') }}</label>
                <p class="font-weight-bold">{{ $po->vendor->name ?? '—' }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">{{ __('Warehouse') }}</label>
                <p class="font-weight-bold">{{ $po->warehouse->name ?? '—' }}</p>
            </div>

            <div class="col-md-4">
                <label class="text-muted">{{ __('Status') }}</label>
                <p>
                    @if($po->status === 'received')
                        <span class="badge badge-success">
                            <i class="fa fa-check"></i> {{ __('Received') }}
                        </span>
                    @else
                        <span class="badge badge-warning text-dark">
                            <i class="fa fa-hourglass-half"></i> {{ __('Draft') }}
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <hr>

        {{-- Items Table --}}
        <h5 class="mb-3"><i class="fa fa-box mr-1"></i> {{ __('Items') }}</h5>

        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>{{ __('Item') }}</th>
                        <th width="15%">{{ __('Qty') }}</th>
                        <th width="15%">{{ __('Unit Price') }}</th>
                        <th width="15%">{{ __('Total') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($po->items as $row)
                        <tr>
                            <td>{{ $row->item_name }}</td>
                            <td>{{ $row->quantity }} {{ $row->unit_type }}</td>
                            <td>AED {{ number_format($row->unit_price, 2) }}</td>
                            <td class="font-weight-bold">
                                AED {{ number_format($row->line_total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr>

        {{-- TOTAL --}}
        <div class="text-right">
            <h4 class="font-weight-bold">
                {{ __('Total') }}:
                <span class="text-primary">AED {{ number_format($po->total_amount, 2) }}</span>
            </h4>
        </div>

        {{-- Receive Button --}}
        <div class="mt-4 text-right">
            @if($po->status !== 'received')
<form id="receiveForm" action="{{ route('admin.purchase-orders.receive', $po) }}" method="POST">
    @csrf
    <button type="button" class="btn btn-success custom-btn" id="receiveBtn">
        <i class="fas fa-check mr-1"></i> {{ __('Mark as Received') }}
    </button>
</form>
            @endif
        </div>

    </div>

</div>
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('receiveBtn').addEventListener('click', function () {

    Swal.fire({
        title: "{{ __('Are you sure?') }}",
        text: "{{ __('Once received, the stock will be added to the warehouse.') }}",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes, receive it!') }}",
        cancelButtonText: "{{ __('Cancel') }}",
        customClass: {
            confirmButton: 'btn btn-success mr-2',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('receiveForm').submit();
        }
    });

});
</script>
@stop


@stop

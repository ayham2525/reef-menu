@extends('adminlte::page')

@section('title', 'Purchase Orders')

@section('content_header')
    <h1 class="mb-3">Purchase Orders</h1>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Purchase Orders</span>
        <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New PO
        </a>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Code</th>
                    <th>Vendor</th>
                    <th>Warehouse</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($purchaseOrders as $po)
                    <tr>
                        <td>{{ $po->code }}</td>
                        <td>{{ optional($po->vendor)->name }}</td>
                        <td>{{ optional($po->warehouse)->name }}</td>

                        <td>{{ number_format($po->total_amount, 2) }}</td>

                        <td>
                            @if($po->status == 'draft')
                                <span class="badge bg-warning">Draft</span>
                            @elseif($po->status == 'received')
                                <span class="badge bg-success">Received</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($po->status) }}</span>
                            @endif
                        </td>

                        <td>{{ $po->created_at->format('Y-m-d') }}</td>

                        <td class="text-end">

                            {{-- Receive Goods --}}
                            @if($po->status !== 'received')
                                <form method="POST"
                                      action="{{ route('admin.purchase-orders.receive', $po->id) }}"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-success btn-sm"
                                            onclick="return confirm('Confirm receiving goods into warehouse?')">
                                        <i class="fas fa-check"></i>
                                        Receive
                                    </button>
                                </form>
                            @endif

                            {{-- View --}}
                            <a href="{{ route('admin.purchase-orders.show', $po->id) }}"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No purchase orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="card-footer">
        {{ $purchaseOrders->links() }}
    </div>
</div>

@endsection

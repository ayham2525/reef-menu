@extends('adminlte::page')

@section('title', __('Orders'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <h1 class="mb-0">
        <i class="fa fa-shopping-cart text-primary"></i> {{ __('Orders Management') }}
    </h1>
    <a href="{{ route('admin.orders.create') }}" class="btn btn-primary">
        <i class="fa fa-plus mr-1"></i> {{ __('Create Order') }}
    </a>
</div>
@stop

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="mb-0"><i class="fa fa-list mr-1"></i> {{ __('Orders List') }}</h5>
        <div class="form-inline">
            <input type="text" id="orderSearch" class="form-control form-control-sm mr-2"
                   placeholder="{{ __('Quick Search...') }}">
            <button id="bulkUpdateBtn" class="btn btn-sm btn-success">
                <i class="fa fa-sync mr-1"></i> {{ __('Bulk Update Status') }}
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:550px;overflow-y:auto;">
            <form id="bulkForm">
                @csrf
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:40px;">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>{{ __('Order Code') }}</th>
                             <th>{{ __('Agency Name') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Placed At') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTable">
                        @forelse($orders as $order)
                            <tr>
                                <td><input type="checkbox" name="selected[]" value="{{ $order->id }}"></td>
                                <td class="font-weight-bold text-monospace">{{ $order->code }}</td>
                                 <td class="font-weight-bold text-monospace">{{ $order->agency_name }}</td>
                                <td><i class="fa fa-coins text-muted"></i> {{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <select class="form-control form-control-sm order-status mr-2"
                                                data-id="{{ $order->id }}">
                                            @foreach(['new','processing','completed','cancelled'] as $status)
                                                <option value="{{ $status }}" {{ $order->status == $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="badge status-badge {{ $order->status }} px-2 py-1">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $order->placed_at ?? $order->created_at->format('Y-m-d H:i') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                       class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa fa-info-circle mr-1"></i>{{ __('No orders found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    @if ($orders->hasPages())
        <div class="card-footer">
            {{ $orders->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>
@stop

@section('css')
<style>
.status-badge.new { background:#007bff; color:#fff; }
.status-badge.processing { background:#ffc107; color:#000; }
.status-badge.completed { background:#28a745; color:#fff; }
.status-badge.cancelled { background:#dc3545; color:#fff; }
</style>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // === Quick Search ===
    document.getElementById('orderSearch').addEventListener('input', function () {
        const term = this.value.toLowerCase();
        document.querySelectorAll('#ordersTable tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });

    // === Select All ===
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('input[name="selected[]"]').forEach(cb => cb.checked = this.checked);
    });

    // === Inline Single Status Update ===
    document.querySelectorAll('.order-status').forEach(select => {
        select.addEventListener('change', function () {
            const orderId = this.dataset.id;
            const status = this.value;
            const badge = this.closest('td').querySelector('.status-badge');
            fetch(`/admin/orders/${orderId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // update badge visually
                    badge.className = 'badge status-badge ' + status + ' px-2 py-1';
                    badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    toastr.success('{{ __("Status updated successfully!") }}');
                } else {
                    toastr.error('{{ __("Failed to update status!") }}');
                }
            });
        });
    });

    // === Bulk Update ===
    document.getElementById('bulkUpdateBtn').addEventListener('click', function (e) {
        e.preventDefault();
        const ids = Array.from(document.querySelectorAll('input[name="selected[]"]:checked')).map(i => i.value);
        if (!ids.length) {
            alert('{{ __("Please select at least one order.") }}');
            return;
        }
        const status = prompt("Enter new status (new, processing, completed, cancelled):");
        if (!status) return;

        fetch(`/admin/orders/bulk-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids, status })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                toastr.success('{{ __("Bulk update completed!") }}');
                setTimeout(() => location.reload(), 1000);
            } else {
                toastr.error('{{ __("Bulk update failed!") }}');
            }
        });
    });
});
</script>
@stop

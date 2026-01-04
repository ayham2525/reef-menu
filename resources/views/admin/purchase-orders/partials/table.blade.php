<div class="table-responsive">
    <table class="table table-hover table-bordered mb-0">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th><i class="fa fa-barcode"></i> {{ __('Code') }}</th>
                <th><i class="fa fa-truck-loading"></i> {{ __('Vendor') }}</th>
                <th><i class="fa fa-warehouse"></i> {{ __('Warehouse') }}</th>
                <th><i class="fa fa-dollar-sign"></i> {{ __('Total') }}</th>
                <th><i class="fa fa-toggle-on"></i> {{ __('Status') }}</th>
                <th class="text-center" width="110"><i class="fa fa-cogs"></i> {{ __('Actions') }}</th>
            </tr>
        </thead>

        <tbody>
        @forelse($orders as $po)
            <tr>
                <td>
                    {{ method_exists($orders, 'firstItem') ? $orders->firstItem() + $loop->index : $loop->iteration }}
                </td>

                <td>{{ $po->code }}</td>
                <td>{{ $po->vendor->name ?? '—' }}</td>
                <td>{{ $po->warehouse->name ?? '—' }}</td>
                <td>{{ number_format($po->total_amount, 2) }}</td>

                <td>
                    @if($po->status === 'received')
                        <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> {{ __('Received') }}
                        </span>
                    @else
                        <span class="badge badge-warning text-dark">
                            <i class="fa fa-clock"></i> {{ __('Draft') }}
                        </span>
                    @endif
                </td>

                <td class="text-center">
                    <a href="{{ route('admin.purchase-orders.show', $po->id) }}"
                       class="text-info mr-2"
                       title="{{ __('View') }}">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle"></i> {{ __('No purchase orders found.') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if ($orders->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $orders->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@endif

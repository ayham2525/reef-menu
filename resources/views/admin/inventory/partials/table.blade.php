<div class="table-responsive">
    <table class="table table-hover table-bordered mb-0">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>{{ __('Item') }}</th>
                <th>{{ __('Warehouse') }}</th>
                <th>{{ __('Quantity') }}</th>
                <th>{{ __('Min') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-center">{{ __('Actions') }}</th>
            </tr>
        </thead>

        <tbody>
        @forelse ($stocks as $s)
            <tr class="{{ $s->is_low_stock ? 'table-danger' : '' }}">
                <td>{{ ($stocks->firstItem() ?? 1) + $loop->index }}</td>

                <td>{{ $s->item->name }}</td>

                <td>{{ $s->warehouse->name ?? 'Default' }}</td>

                <td>{{ $s->quantity }} {{ $s->unit_type }}</td>

                <td>{{ $s->min_quantity }} {{ $s->unit_type }}</td>

                <td>
                    @if ($s->is_low_stock)
                        <span class="badge badge-danger">
                            <i class="fa fa-exclamation-circle"></i> {{ __('Low Stock') }}
                        </span>
                    @else
                        <span class="badge badge-success">
                            <i class="fa fa-check"></i> {{ __('OK') }}
                        </span>
                    @endif
                </td>

                <td class="text-center">
                    <a href="{{ route('admin.inventory.show', $s->id) }}"
                       class="text-primary"
                       title="{{ __('View') }}">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            </tr>

        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="fa fa-info-circle"></i> {{ __('No stock items found.') }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if ($stocks->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $stocks->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@endif

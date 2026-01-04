<div class="table-responsive">
    <table class="table table-hover table-bordered mb-0">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th><i class="fa fa-warehouse"></i> {{ __('Name') }}</th>
                <th><i class="fa fa-barcode"></i> {{ __('Code') }}</th>
                <th><i class="fa fa-map-marker-alt"></i> {{ __('Location') }}</th>
                <th><i class="fa fa-toggle-on"></i> {{ __('Status') }}</th>
                <th class="text-center" width="120"><i class="fa fa-cogs"></i> {{ __('Actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @forelse($warehouses as $w)
                <tr>
                    <td>{{ $warehouses->firstItem() + $loop->index }}</td>

                    <td>{{ $w->name }}</td>
                    <td>{{ $w->code }}</td>
                    <td>{{ $w->location ?: '—' }}</td>

                    <td>
                        @if($w->is_active)
                            <span class="badge badge-success"><i class="fa fa-check"></i> {{ __('Active') }}</span>
                        @else
                            <span class="badge badge-secondary"><i class="fa fa-ban"></i> {{ __('Inactive') }}</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <a href="{{ route('admin.warehouses.edit', $w->id) }}" class="text-info mr-2">
                            <i class="fa fa-pen"></i>
                        </a>

                        <form action="{{ route('admin.warehouses.destroy', $w->id) }}"
                              method="POST"
                              class="d-inline delete-form">
                            @csrf @method('DELETE')
                            <a href="#" class="text-danger delete-btn">
                                <i class="fa fa-trash"></i>
                            </a>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fa fa-info-circle"></i> {{ __('No warehouses found.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
</div>

@if ($warehouses->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $warehouses->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@endif

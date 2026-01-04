<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 70px;"></th>
                        <th>{{ __('Name / SKU') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th style="width: 140px;">{{ __('Price') }}</th>
                        <th style="width: 140px;">{{ __('Flags') }}</th>
                        <th style="width: 100px;">{{ __('Order') }}</th>
                        <th style="width: 200px;" class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>
                                @if($item->primary_image_url)
                                    <img src="{{ $item->primary_image_url }}" alt="" class="img-thumbnail" style="width:64px;height:48px;object-fit:cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="width:64px;height:48px;border:1px dashed #ddd;">
                                        <i class="fa fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-600">
                                    <i class="fa fa-utensils mr-1 text-muted"></i>{{ $item->name }}
                                </div>
                                <div class="text-muted small">SKU: <span class="text-monospace">{{ $item->sku ?: '—' }}</span></div>
                                <div class="text-muted small">/{{ $item->slug }}</div>
                            </td>
                            <td>{{ optional($item->category)->name ?? '—' }}</td>
                            <td>
                               <i class="fa fa-coins"></i>
                                {{ number_format($item->price, 2) }} {{ $item->currency }}
                            </td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge badge-success"><i class="fa fa-check mr-1"></i>{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fa fa-ban mr-1"></i>{{ __('Inactive') }}</span>
                                @endif
                                @if($item->is_available)
                                    <span class="badge badge-info"><i class="fa fa-store mr-1"></i>{{ __('Available') }}</span>
                                @endif
                                @if($item->is_featured)
                                    <span class="badge badge-warning"><i class="fa fa-star mr-1"></i>{{ __('Featured') }}</span>
                                @endif
                            </td>
                            <td>{{ $item->sort_order }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.menu-items.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger js-delete-link" title="{{ __('Delete') }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa fa-info-circle mr-1"></i>{{ __('No items found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($items->hasPages())
        <div class="card-footer">
           {{ $items->onEachSide(1)->links('pagination::bootstrap-4') }}

        </div>
    @endif
</div>

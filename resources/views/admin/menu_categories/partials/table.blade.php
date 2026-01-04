{{-- resources/views/admin/menu_categories/partials/table.blade.php --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th style="width: 120px;">{{ __('Code') }}</th>
                        <th>{{ __('Parent') }}</th>
                        <th style="width: 120px;">{{ __('Status') }}</th>
                        <th style="width: 100px;">{{ __('Order') }}</th>
                        <th style="width: 180px;" class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $c)
                        <tr>
                            <td>
                                <div class="font-weight-600"><i class="fa fa-folder mr-1 text-muted"></i>{{ $c->name }}</div>
                                <div class="text-muted small">/{{ $c->slug }}</div>
                            </td>
                            <td><span class="text-monospace">{{ $c->code ?? '—' }}</span></td>
                            <td>{{ optional($c->parent)->name ?? '—' }}</td>
                            <td>
                                @if($c->is_active)
                                    <span class="badge badge-success"><i class="fa fa-check mr-1"></i>{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fa fa-ban mr-1"></i>{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $c->sort_order }}</td>
                            <td class="text-right">
                                <a href="{{ route('admin.menu-categories.show', $c) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View') }}">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.menu-categories.edit', $c) }}" class="btn btn-sm btn-primary" title="{{ __('Edit') }}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.menu-categories.destroy', $c) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger js-delete-link" title="{{ __('Delete') }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fa fa-info-circle mr-1"></i>{{ __('No categories found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($categories->hasPages())
        <div class="card-footer">
            {{ $categories->onEachSide(1)->links() }}
        </div>
    @endif
</div>

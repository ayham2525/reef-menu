<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 110px;">{{ __('Code') }}</th>
                        <th>{{ __('Name / Email') }}</th>
                        <th>{{ __('Position') }}</th>
                        <th>{{ __('Section') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th style="width: 120px;">{{ __('Status') }}</th>
                        <th style="width: 180px;" class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $e)
                        <tr>
                            <td><span class="text-monospace">{{ $e->employee_code }}</span></td>
                            <td>
                                <div class="font-weight-600">{{ $e->user->name ?? '—' }}</div>
                                <div class="text-muted small">{{ $e->user->email ?? '—' }}</div>
                            </td>
                            <td>{{ $e->position->name ?? '—' }}</td>
                            <td>{{ $e->section->name ?? '—' }}</td>
                            <td>{{ $e->phone ?? '—' }}</td>
                            <td>
                                @if($e->is_active)
                                    <span class="badge badge-success"><i class="fa fa-check mr-1"></i>{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fa fa-ban mr-1"></i>{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.employees.show', $e) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.employees.edit', $e) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.employees.destroy', $e) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-danger js-delete-link">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fa fa-info-circle mr-1"></i>{{ __('No employees found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($employees->hasPages())
        <div class="card-footer">
            {{ $employees->onEachSide(1)->links() }}
        </div>
    @endif
</div>

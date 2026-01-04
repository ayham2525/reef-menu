<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th><i class="fa fa-tag"></i> {{ __('Name') }}</th>
                    <th><i class="fa fa-code"></i> {{ __('Code') }}</th>
                    <th><i class="fa fa-link"></i> {{ __('Slug') }}</th>
                    <th><i class="fa fa-sitemap"></i> {{ __('Parent') }}</th>
                    <th><i class="fa fa-toggle-on"></i> {{ __('Status') }}</th>
                    <th class="text-center"><i class="fa fa-cogs"></i> {{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sections as $section)
                    <tr>
                        <td>{{ $section->id }}</td>
                        <td>
                            <a href="{{ route('admin.sections.show', $section) }}" class="text-primary">
                                <i class="fa fa-folder-open"></i> {{ $section->name }}
                            </a>
                        </td>
                        <td>{{ $section->code ?? '-' }}</td>
                        <td>{{ $section->slug }}</td>
                        <td>{{ $section->parent?->name ?? '-' }}</td>
                        <td>
                            @if($section->is_active)
                                <span class="badge badge-success">
                                    <i class="fa fa-check-circle"></i> {{ __('Active') }}
                                </span>
                            @else
                                <span class="badge badge-secondary">
                                    <i class="fa fa-ban"></i> {{ __('Inactive') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.sections.show', $section) }}" class="text-primary me-3" title="{{ __('View') }}">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.sections.edit', $section) }}" class="text-info me-3" title="{{ __('Edit') }}">
                                <i class="fa fa-pen"></i>
                            </a>

                            {{-- Delete (SweetAlert2) --}}
                            <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <a href="#" class="text-danger js-delete-link" title="{{ __('Delete') }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fa fa-info-circle"></i> {{ __('No sections found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($sections->hasPages())
        <div class="card-footer">
            {{-- Keep Laravel’s links; JS will hijack clicks for AJAX --}}
            {{ $sections->links() }}
        </div>
    @endif
</div>

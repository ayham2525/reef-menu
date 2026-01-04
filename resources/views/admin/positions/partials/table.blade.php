@php
    $q = trim((string) request('search', ''));
@endphp

<div class="card">
    @if ($positions->count() === 0)
        {{-- Pretty empty state (no rows) --}}
        <div class="card-body text-center text-muted py-5">
            <div class="mb-2">
                <i class="fa fa-info-circle fa-2x"></i>
            </div>
            <h5 class="mb-2">{{ __('No positions found.') }}</h5>

            @if($q !== '')
                <p class="mb-3">
                    {{ __('No results for') }}
                    <strong class="text-dark">“{{ e($q) }}”</strong>.
                </p>
                <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-times"></i> {{ __('Clear filters') }}
                </a>
            @endif

            <a href="{{ route('admin.positions.create') }}" class="btn btn-primary custom-btn">
                <i class="fa fa-plus"></i> {{ __('Create Position') }}
            </a>
        </div>
    @else
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th><i class="fa fa-briefcase"></i> {{ __('Name') }}</th>
                        <th><i class="fa fa-code"></i> {{ __('Code') }}</th>
                        <th><i class="fa fa-link"></i> {{ __('Slug') }}</th>
                        <th><i class="fa fa-sort-numeric-down"></i> {{ __('Sort Order') }}</th>
                        <th><i class="fa fa-toggle-on"></i> {{ __('Status') }}</th>
                        <th class="text-center"><i class="fa fa-cogs"></i> {{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($positions as $position)
                        <tr>
                            <td>{{ $position->id }}</td>

                            <td>
                                <a href="{{ route('admin.positions.show', $position) }}" class="text-primary">
                                    <i class="fa fa-briefcase"></i> {{ $position->name }}
                                </a>
                                @if (!empty($position->description))
                                    <div class="text-muted small mt-1" title="{{ $position->description }}">
                                        {{ \Illuminate\Support\Str::limit($position->description, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td>{{ $position->code ?? '-' }}</td>
                            <td>{{ $position->slug ?? '-' }}</td>
                            <td>{{ (int) $position->sort_order }}</td>

                            <td>
                                @if ($position->is_active)
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
                                {{-- View --}}
                                <a href="{{ route('admin.positions.show', $position) }}" class="text-primary mr-3" title="{{ __('View') }}">
                                    <i class="fa fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('admin.positions.edit', $position) }}" class="text-info mr-3" title="{{ __('Edit') }}">
                                    <i class="fa fa-pen"></i>
                                </a>

                                {{-- Delete (SweetAlert2 will intercept .js-delete-link) --}}
                                <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <a href="#" class="text-danger js-delete-link" title="{{ __('Delete') }}">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($positions->hasPages())
            <div class="card-footer">
                {{-- Keep Laravel pagination; your JS hijacks the links for AJAX --}}
                {{ $positions->links() }}
            </div>
        @endif
    @endif
</div>

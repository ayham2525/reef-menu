<div class="table-responsive">
    <table class="table table-hover table-bordered mb-0">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th><i class="fa fa-user"></i> {{ __('Name') }}</th>
                <th><i class="fa fa-envelope"></i> {{ __('Email') }}</th>
                <th><i class="fa fa-phone"></i> {{ __('Phone') }}</th>
                <th><i class="fa fa-id-card"></i> {{ __('BRN') }}</th>
                <th><i class="fa fa-building"></i> {{ __('Agency') }}</th>
                <th><i class="fa fa-toggle-on"></i> {{ __('Status') }}</th>
                <th class="text-center"><i class="fa fa-cogs"></i> {{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($brokers as $broker)
                <tr>
                    {{-- Row number, not the real ID --}}
                    <td>
                        @if(method_exists($brokers, 'firstItem') && !is_null($brokers->firstItem()))
                            {{ $brokers->firstItem() + $loop->index }}
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </td>

                    {{-- Name --}}
                    <td>
                        <a href="{{ route('admin.brokers.show', $broker) }}" class="text-primary">
                            {{ $broker->name }}
                        </a>
                    </td>

                    {{-- Details --}}
                    <td>{{ $broker->email ?? '—' }}</td>
                    <td>{{ $broker->phone ?? '—' }}</td>
                    <td>{{ $broker->brn ?? '—' }}</td>
                    <td>{{ $broker->agency->name ?? '—' }}</td>

                    {{-- Status --}}
                    <td>
                        @if($broker->is_active)
                            <span class="badge badge-success">
                                <i class="fa fa-check-circle"></i> {{ __('Active') }}
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fa fa-ban"></i> {{ __('Inactive') }}
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
                        <a href="{{ route('admin.brokers.show', $broker) }}" class="text-primary me-3" title="{{ __('View') }}">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('admin.brokers.edit', $broker) }}" class="text-info me-3" title="{{ __('Edit') }}">
                            <i class="fa fa-pen"></i>
                        </a>

                        <form action="{{ route('admin.brokers.destroy', $broker) }}" method="POST" class="d-inline">
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
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fa fa-info-circle"></i> {{ __('No brokers found.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Bootstrap 4 pagination --}}
@if ($brokers->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $brokers->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@endif

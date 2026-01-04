<div class="table-responsive">
    <table class="table table-hover table-bordered mb-0">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th><i class="fa fa-truck"></i> {{ __('Name') }}</th>
                <th><i class="fa fa-user"></i> {{ __('Contact Person') }}</th>
                <th><i class="fa fa-phone"></i> {{ __('Phone') }}</th>
                <th><i class="fa fa-envelope"></i> {{ __('Email') }}</th>
                <th><i class="fa fa-map-marker-alt"></i> {{ __('Address') }}</th>
                <th><i class="fa fa-toggle-on"></i> {{ __('Status') }}</th>
                <th class="text-center"><i class="fa fa-cogs"></i> {{ __('Actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($vendors as $vendor)
                <tr>
                    {{-- Row number --}}
                    <td>
                        @if(method_exists($vendors, 'firstItem'))
                            {{ $vendors->firstItem() + $loop->index }}
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </td>

                    <td>{{ $vendor->name }}</td>
                    <td>{{ $vendor->contact_person ?? '—' }}</td>
                    <td>{{ $vendor->phone ?? '—' }}</td>
                    <td>{{ $vendor->email ?? '—' }}</td>
                    <td>{{ $vendor->address ?? '—' }}</td>

                    <td>
                        @if($vendor->is_active)
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

                        <a href="{{ route('admin.vendors.edit', $vendor) }}"
                           class="text-info me-3" title="{{ __('Edit') }}">
                            <i class="fa fa-pen"></i>
                        </a>

                        <form action="{{ route('admin.vendors.destroy', $vendor) }}"
                              method="POST" class="d-inline">
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
                        <i class="fa fa-info-circle"></i> {{ __('No vendors found.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($vendors->hasPages())
    <div class="mt-3 d-flex justify-content-center">
        {{ $vendors->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
@endif

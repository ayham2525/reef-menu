<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-bordered mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th><i class="fa fa-tag"></i> {{ __('Name') }}</th>
                    <th><i class="fa fa-code"></i> {{ __('Code') }}</th>
                    <th><i class="fa fa-id-card"></i> {{ __('License') }}</th>
                    <th><i class="fa fa-envelope"></i> {{ __('Email') }}</th>
                    <th><i class="fa fa-phone"></i> {{ __('Phone') }}</th>

                    {{-- NEW: Brokers count --}}
                    <th><i class="fa fa-users"></i> {{ __('Brokers') }}</th>

                    <th><i class="fa fa-toggle-on"></i> {{ __('Status') }}</th>
                    <th class="text-center"><i class="fa fa-cogs"></i> {{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($agencies as $agency)
                    <tr>
                        {{-- Sequential index instead of real ID --}}
                        <td>
                            @if(method_exists($agencies, 'firstItem') && !is_null($agencies->firstItem()))
                                {{ $agencies->firstItem() + $loop->index }}
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('admin.agencies.show', $agency) }}" class="text-primary">
                                <i class="fa fa-building"></i> {{ $agency->name }}
                            </a>
                        </td>
                        <td>{{ $agency->code }}</td>
                        <td>{{ $agency->license_no ?? '—' }}</td>
                        <td>{{ $agency->email ?? '—' }}</td>
                        <td>{{ $agency->phone ?? '—' }}</td>

                        {{-- NEW: Clickable brokers count → goes to Brokers index filtered by this agency --}}
                        <td>
                            <a
                                href="{{ route('admin.brokers.index', ['agency_id' => $agency->id]) }}"
                                class="badge badge-info"
                                title="{{ __('View brokers for :name', ['name' => $agency->name]) }}"
                            >
                                {{ $agency->brokers_count ?? $agency->brokers()->count() }}
                            </a>
                        </td>

                        <td>
                            @if($agency->is_active)
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
                            {{-- NEW: quick link to related brokers (icon) --}}
                            <a href="{{ route('admin.brokers.index', ['agency_id' => $agency->id]) }}"
                               class="text-secondary mr-2" title="{{ __('Related Brokers') }}">
                                <i class="fa fa-users"></i>
                            </a>

                            <a href="{{ route('admin.agencies.show', $agency) }}" class="text-primary mr-2" title="{{ __('View') }}">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.agencies.edit', $agency) }}" class="text-info mr-2" title="{{ __('Edit') }}">
                                <i class="fa fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.agencies.destroy', $agency) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <a href="#" class="text-danger js-delete-link" title="{{ __('Delete') }}">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fa fa-info-circle"></i> {{ __('No agencies found.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($agencies->hasPages())
        <div class="card-footer">
            {{ $agencies->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    @endif
</div>

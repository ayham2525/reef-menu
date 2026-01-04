@extends('adminlte::page')

@section('title', __('Broker Details'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-id-badge text-primary"></i> {{ __('Broker Details') }}
        </h1>
        <div>
            <a href="{{ route('admin.brokers.edit', $broker) }}" class="btn btn-warning mr-2">
                <i class="fa fa-edit"></i> {{ __('Edit') }}
            </a>
            <a href="{{ route('admin.brokers.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left"></i> {{ __('Back to Brokers') }}
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="fa fa-user mr-1 text-muted"></i> {{ $broker->name }}</h3>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <h5 class="text-muted">{{ __('Basic Information') }}</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width:30%">{{ __('Name') }}</th>
                            <td>{{ $broker->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <td>{{ $broker->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Phone') }}</th>
                            <td>{{ $broker->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('BRN') }}</th>
                            <td>{{ $broker->brn ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h5 class="text-muted">{{ __('Agency & Status') }}</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width:30%">{{ __('Agency') }}</th>
                            <td>{{ $broker->agency->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if($broker->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}</th>
                            <td>{{ $broker->created_at ? $broker->created_at->format('d M Y, h:i A') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Last Updated') }}</th>
                            <td>{{ $broker->updated_at ? $broker->updated_at->format('d M Y, h:i A') : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="border-top pt-3 d-flex justify-content-between">
                <div>
                    <a href="{{ route('admin.brokers.edit', $broker) }}" class="btn btn-primary">
                        <i class="fa fa-edit"></i> {{ __('Edit Broker') }}
                    </a>
                    <a href="{{ route('admin.brokers.index') }}" class="btn btn-light">
                        {{ __('Back') }}
                    </a>
                </div>

                {{-- Optional delete (method spoofing) --}}
                <form action="{{ route('admin.brokers.destroy', $broker) }}" method="POST" onsubmit="return confirmDelete(event)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(e){
    e.preventDefault();
    const form = e.target;
    Swal.fire({
        title: "{{ __('Delete this broker?') }}",
        text: "{{ __('This action cannot be undone.') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: "{{ __('Yes, delete it') }}",
        cancelButtonText: "{{ __('Cancel') }}",
        customClass: { confirmButton: 'btn btn-danger mr-2', cancelButton: 'btn btn-secondary' },
        buttonsStyling: false
    }).then((res) => { if (res.isConfirmed) form.submit(); });
    return false;
}
</script>
@stop

{{-- resources/views/admin/employees/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('Employees') . ' - ' . __('Details'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-id-badge text-primary mr-2"></i>{{ __('Employee Details') }}
            <small class="ml-2 text-muted">({{ $employee->employee_code }})</small>
        </h1>

        <div class="btn-group">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left mr-1"></i>{{ __('Back to List') }}
            </a>
            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn btn-primary">
                <i class="fa fa-edit mr-1"></i>{{ __('Edit') }}
            </a>
            <form action="{{ route('admin.employees.destroy', $employee) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger js-delete-link">
                    <i class="fa fa-trash mr-1"></i>{{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- Left column: main info --}}
        <div class="col-md-8">
            {{-- Summary --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-user mr-2 text-primary"></i>{{ __('Summary') }}
                    </h3>
                    <div>
                        @if($employee->is_active)
                            <span class="badge badge-success">
                                <i class="fa fa-check mr-1"></i>{{ __('Active') }}
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fa fa-ban mr-1"></i>{{ __('Inactive') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4"><i class="fa fa-user mr-1 text-muted"></i>{{ __('Name') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->user)->name ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-envelope mr-1 text-muted"></i>{{ __('Email') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->user)->email ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-hashtag mr-1 text-muted"></i>{{ __('Employee Code') }}</dt>
                        <dd class="col-sm-8"><span class="text-monospace">{{ $employee->employee_code }}</span></dd>

                        <dt class="col-sm-4"><i class="fa fa-briefcase mr-1 text-muted"></i>{{ __('Position') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->position)->name ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-sitemap mr-1 text-muted"></i>{{ __('Section') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->section)->name ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-phone mr-1 text-muted"></i>{{ __('Phone') }}</dt>
                        <dd class="col-sm-8">{{ $employee->phone ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-id-card mr-1 text-muted"></i>{{ __('National ID') }}</dt>
                        <dd class="col-sm-8">{{ $employee->national_id ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-venus-mars mr-1 text-muted"></i>{{ __('Gender') }}</dt>
                        <dd class="col-sm-8">
                            @php
                                $g = $employee->gender;
                                $gLabel = $g ? __(\Illuminate\Support\Str::ucfirst($g)) : '—';
                            @endphp
                            {{ $gLabel }}
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- Employment & Personal Dates --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-calendar-alt mr-2 text-primary"></i>{{ __('Dates') }}
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4"><i class="fa fa-birthday-cake mr-1 text-muted"></i>{{ __('Birth Date') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->birth_date)->format('Y-m-d') ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-user-clock mr-1 text-muted"></i>{{ __('Hired At') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->hired_at)->format('Y-m-d') ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-user-times mr-1 text-muted"></i>{{ __('Terminated At') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->terminated_at)->format('Y-m-d') ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-clock mr-1 text-muted"></i>{{ __('Created At') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->created_at)->format('Y-m-d H:i') ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-sync mr-1 text-muted"></i>{{ __('Updated At') }}</dt>
                        <dd class="col-sm-8">{{ optional($employee->updated_at)->format('Y-m-d H:i') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Notes --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-sticky-note mr-2 text-primary"></i>{{ __('Notes') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if($employee->notes)
                        <p class="mb-0" style="white-space: pre-line;">{{ $employee->notes }}</p>
                    @else
                        <span class="text-muted"><i class="fa fa-info-circle mr-1"></i>{{ __('No notes available.') }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column: meta / quick info --}}
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-info-circle mr-2 text-primary"></i>{{ __('Quick Info') }}
                    </h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fa fa-user mr-1 text-muted"></i>
                            <strong>{{ __('User ID') }}:</strong>
                            <span class="ml-1">{{ $employee->user_id }}</span>
                        </li>
                        <li class="mb-2">
                            <i class="fa fa-briefcase mr-1 text-muted"></i>
                            <strong>{{ __('Position ID') }}:</strong>
                            <span class="ml-1">{{ $employee->position_id }}</span>
                        </li>
                        <li class="mb-2">
                            <i class="fa fa-sitemap mr-1 text-muted"></i>
                            <strong>{{ __('Section ID') }}:</strong>
                            <span class="ml-1">{{ $employee->section_id ?? '—' }}</span>
                        </li>
                        <li class="mb-2">
                            <i class="fa fa-user-plus mr-1 text-muted"></i>
                            <strong>{{ __('Created By') }}:</strong>
                            <span class="ml-1">{{ $employee->created_by ?? '—' }}</span>
                        </li>
                        <li>
                            <i class="fa fa-user-edit mr-1 text-muted"></i>
                            <strong>{{ __('Updated By') }}:</strong>
                            <span class="ml-1">{{ $employee->updated_by ?? '—' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- You can add more side widgets here if needed --}}
        </div>
    </div>
@stop

@section('js')
    {{-- SweetAlert2 for delete confirmation --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            const deleteBtns = document.querySelectorAll('.js-delete-link');
            deleteBtns.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: "{{ __('Delete this employee?') }}",
                        text: "{{ __('This action cannot be undone.') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, delete it') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                        customClass: {
                            confirmButton: 'btn btn-danger mr-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed && form) form.submit();
                    });
                });
            });
        })();
    </script>
@stop

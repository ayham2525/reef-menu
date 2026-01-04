@extends('adminlte::page')

@section('title', __('Edit Agency'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-building text-primary"></i> {{ __('Edit Agency') }} — {{ $agency->name }}
        </h1>
        <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back to Agencies') }}
        </a>
    </div>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            <strong>{{ __('Please fix the errors below:') }}</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form id="form-update" action="{{ route('admin.agencies.update', $agency) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Agency Details') }}</h3>
            </div>

            <div class="card-body">
                {{-- Name --}}
                <div class="form-group">
                    <label for="name"><i class="fa fa-tag mr-1 text-muted"></i> {{ __('Name') }}</label>
                    <input type="text" id="name" name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $agency->name) }}" required autofocus>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Code / License --}}
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="code"><i class="fa fa-code mr-1 text-muted"></i> {{ __('Code') }}</label>
                        <input type="text" id="code" name="code"
                            class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code', $agency->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="license_no"><i class="fa fa-id-card mr-1 text-muted"></i> {{ __('License No.') }}</label>
                        <input type="text" id="license_no" name="license_no"
                            class="form-control @error('license_no') is-invalid @enderror"
                            value="{{ old('license_no', $agency->license_no) }}">
                        @error('license_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Email / Phone --}}
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="email"><i class="fa fa-envelope mr-1 text-muted"></i> {{ __('Email') }}</label>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $agency->email) }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="phone"><i class="fa fa-phone mr-1 text-muted"></i> {{ __('Phone') }}</label>
                        <input type="text" id="phone" name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $agency->phone) }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="form-group">
                    <label for="is_active"><i class="fa fa-toggle-on mr-1 text-muted"></i> {{ __('Status') }}</label>
                    <select id="is_active" name="is_active"
                        class="form-control @error('is_active') is-invalid @enderror">
                        <option value="1" @selected(old('is_active', (int) $agency->is_active) == 1)>
                            {{ __('Active') }}</option>
                        <option value="0" @selected(old('is_active', (int) $agency->is_active) == 0)>
                            {{ __('Inactive') }}</option>
                    </select>
                    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>

                    {{-- SweetAlert2 Delete --}}
                    <button type="button" id="btn-delete" class="btn btn-outline-danger mr-2">
                        <i class="fa fa-trash"></i> {{ __('Delete') }}
                    </button>

                    {{-- Update --}}
                    <button type="submit" class="btn btn-primary custom-btn">
                        <i class="fa fa-save mr-1"></i> {{ __('Update') }}
                    </button>

            </div>
        </div>
    </form>

    {{-- Hidden DELETE form --}}
    <form id="form-delete" action="{{ route('admin.agencies.destroy', $agency) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteBtn = document.getElementById('btn-delete');
            const deleteForm = document.getElementById('form-delete');

            deleteBtn.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: "{{ __('Delete this agency?') }}",
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
                    if (result.isConfirmed) {
                        deleteForm.submit();
                    }
                });
            });
        });
    </script>
@stop

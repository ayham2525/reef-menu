{{-- resources/views/admin/employees/create.blade.php --}}
@extends('adminlte::page')

@section('title', __('Employees') . ' - ' . __('Create'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-user-plus text-primary mr-2"></i>{{ __('Create Employee') }}
    </h1>
    <a href="{{ route('admin.employees.index') }}" class="btn btn-secondary">
        <i class="fa fa-arrow-left mr-1"></i>{{ __('Back to List') }}
    </a>
</div>
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger"><strong>{{ __('Please fix the errors below') }}</strong></div>
@endif

<form action="{{ route('admin.employees.store') }}" method="POST" autocomplete="off">
    @csrf
    <div class="card shadow-sm">
        <div class="card-header"><i class="fa fa-id-card mr-1"></i>{{ __('User Account') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label>{{ __('Full Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="user_name" class="form-control @error('user_name') is-invalid @enderror"
                           value="{{ old('user_name') }}" required>
                    @error('user_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label>{{ __('Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="user_email" class="form-control @error('user_email') is-invalid @enderror"
                           value="{{ old('user_email') }}" required>
                    @error('user_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mt-3">
                    <label>{{ __('Password') }} <span class="text-danger">*</span></label>
                    <input type="password" name="user_password" class="form-control @error('user_password') is-invalid @enderror" required>
                    @error('user_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mt-3">
                    <label>{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                    <input type="password" name="user_password_confirmation" class="form-control" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-header"><i class="fa fa-briefcase mr-1"></i>{{ __('Employee Details') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label>{{ __('Position') }} <span class="text-danger">*</span></label>
                    <select name="position_id" class="form-control @error('position_id') is-invalid @enderror" required>
                        <option value="">{{ __('Select...') }}</option>
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}" {{ old('position_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label>{{ __('Section') }}</label>
                    <select name="section_id" class="form-control @error('section_id') is-invalid @enderror">
                        <option value="">{{ __('None') }}</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ old('section_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('section_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Employee Code') }}</label>
                    <input type="text" name="employee_code" value="{{ old('employee_code') }}" class="form-control @error('employee_code') is-invalid @enderror">
                    @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('National ID') }}</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" class="form-control @error('national_id') is-invalid @enderror">
                    @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Gender') }}</label>
                    <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                        <option value="">{{ __('Not set') }}</option>
                        @foreach(['male'=>__('Male'),'female'=>__('Female'),'other'=>__('Other')] as $k=>$v)
                            <option value="{{ $k }}" {{ old('gender')===$k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Birth Date') }}</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="form-control @error('birth_date') is-invalid @enderror">
                    @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Hired At') }}</label>
                    <input type="date" name="hired_at" value="{{ old('hired_at') }}" class="form-control @error('hired_at') is-invalid @enderror">
                    @error('hired_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Terminated At') }}</label>
                    <input type="date" name="terminated_at" value="{{ old('terminated_at') }}" class="form-control @error('terminated_at') is-invalid @enderror">
                    @error('terminated_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label class="d-block">{{ __('Active') }}</label>
                    <input type="hidden" name="is_active" value="0"><!-- ensure false is sent -->
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('Enable this employee') }}</label>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <label>{{ __('Notes') }}</label>
                    <textarea name="notes" rows="4" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save mr-1"></i>{{ __('Save') }}
            </button>
        </div>
    </div>
</form>
@stop

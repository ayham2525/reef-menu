@extends('adminlte::page')

@section('title', __('Create Agency'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-building text-primary"></i> {{ __('Create Agency') }}
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
                @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.agencies.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card">
            <div class="card-header"><h3 class="card-title">{{ __('Agency Details') }}</h3></div>
            <div class="card-body">
                <div class="form-group">
                    <label for="name"><i class="fa fa-tag mr-1 text-muted"></i> {{ __('Name') }}</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required autofocus>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="code"><i class="fa fa-code mr-1 text-muted"></i> {{ __('Code') }}</label>
                        <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code') }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="license_no"><i class="fa fa-id-card mr-1 text-muted"></i> {{ __('License No.') }}</label>
                        <input type="text" id="license_no" name="license_no"
                               class="form-control @error('license_no') is-invalid @enderror" value="{{ old('license_no') }}">
                        @error('license_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="email"><i class="fa fa-envelope mr-1 text-muted"></i> {{ __('Email') }}</label>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="phone"><i class="fa fa-phone mr-1 text-muted"></i> {{ __('Phone') }}</label>
                        <input type="text" id="phone" name="phone"
                               class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="is_active"><i class="fa fa-toggle-on mr-1 text-muted"></i> {{ __('Status') }}</label>
                    <select id="is_active" name="is_active" class="form-control @error('is_active') is-invalid @enderror">
                        <option value="1" @selected(old('is_active', 1)==1)>{{ __('Active') }}</option>
                        <option value="0" @selected(old('is_active', 1)==0)>{{ __('Inactive') }}</option>
                    </select>
                    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-times"></i> {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary custom-btn">
                    <i class="fa fa-save mr-1"></i> {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
@stop

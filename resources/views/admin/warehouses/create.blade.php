@extends('adminlte::page')

@section('title', __('Add Warehouse'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-warehouse text-primary"></i> {{ __('Add Warehouse') }}
    </h1>

    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left"></i> {{ __('Back to Warehouses') }}
    </a>
</div>
@stop

@section('content')

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-exclamation-triangle mr-2"></i>
        <strong>{{ __('Please fix the errors below:') }}</strong>

        <ul class="mt-2 mb-0 pl-3">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>

        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<form method="POST" action="{{ route('admin.warehouses.store') }}" autocomplete="off">
    @csrf

    <div class="card shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-info-circle mr-1 text-muted"></i> {{ __('Warehouse Details') }}
            </h3>
        </div>

        <div class="card-body">

            <div class="form-row">

                {{-- Warehouse Name --}}
                <div class="form-group col-md-6">
                    <label for="name">
                        <i class="fa fa-warehouse mr-1 text-muted"></i> {{ __('Name') }}
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required
                           placeholder="{{ __('Enter warehouse name') }}">

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Code --}}
                <div class="form-group col-md-6">
                    <label for="code">
                        <i class="fa fa-barcode mr-1 text-muted"></i> {{ __('Code') }}
                    </label>

                    <input type="text" id="code" name="code"
                           class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code') }}"
                           placeholder="{{ __('Code (optional)') }}">

                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="form-row">

                {{-- Location --}}
                <div class="form-group col-md-6">
                    <label for="location">
                        <i class="fa fa-map-marker-alt mr-1 text-muted"></i> {{ __('Location') }}
                    </label>

                    <input type="text" id="location" name="location"
                           class="form-control @error('location') is-invalid @enderror"
                           value="{{ old('location') }}"
                           placeholder="{{ __('Warehouse location') }}">

                    @error('location')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-group col-md-6">
                    <label for="is_active">
                        <i class="fa fa-toggle-on mr-1 text-muted"></i> {{ __('Status') }}
                    </label>

                    <select id="is_active" name="is_active"
                            class="form-control @error('is_active') is-invalid @enderror">
                        <option value="1" @selected(old('is_active', 1)==1)>
                            {{ __('Active') }}
                        </option>
                        <option value="0" @selected(old('is_active', 1)==0)>
                            {{ __('Inactive') }}
                        </option>
                    </select>

                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

        </div>

        {{-- FOOTER BUTTONS --}}
        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary mr-2">
                <i class="fa fa-times"></i> {{ __('Cancel') }}
            </a>

            <button class="btn btn-primary custom-btn">
                <i class="fa fa-save mr-1"></i> {{ __('Save Warehouse') }}
            </button>
        </div>

    </div>

</form>

@stop

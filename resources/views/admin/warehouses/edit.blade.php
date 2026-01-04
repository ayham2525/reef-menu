@extends('adminlte::page')

@section('title', __('Edit Warehouse') . ': ' . $warehouse->name)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-warehouse text-primary"></i>
        {{ __('Edit Warehouse') }} — {{ $warehouse->name }}
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

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif


<form action="{{ route('admin.warehouses.update', $warehouse->id) }}"
      method="POST"
      autocomplete="off">

    @csrf
    @method('PUT')

    <div class="card shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fa fa-info-circle mr-1 text-muted"></i>
                {{ __('Warehouse Details') }}
            </h3>
        </div>

        <div class="card-body">

            <div class="form-row">

                {{-- Name --}}
                <div class="form-group col-md-6">
                    <label class="form-label">
                        <i class="fa fa-warehouse mr-1 text-muted"></i> {{ __('Warehouse Name') }}
                        <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $warehouse->name) }}"
                           required>

                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Code --}}
                <div class="form-group col-md-3">
                    <label class="form-label">
                        <i class="fa fa-barcode mr-1 text-muted"></i> {{ __('Code') }}
                    </label>

                    <input type="text"
                           name="code"
                           class="form-control @error('code') is-invalid @enderror"
                           value="{{ old('code', $warehouse->code) }}">

                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div class="form-group col-md-3">
                    <label class="form-label">
                        <i class="fa fa-toggle-on mr-1 text-muted"></i> {{ __('Status') }}
                    </label>

                    <select name="is_active"
                            class="form-control @error('is_active') is-invalid @enderror">
                        <option value="1" @selected(old('is_active', $warehouse->is_active)==1)>
                            {{ __('Active') }}
                        </option>
                        <option value="0" @selected(old('is_active', $warehouse->is_active)==0)>
                            {{ __('Inactive') }}
                        </option>
                    </select>

                    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>


            <div class="form-row">

                {{-- Location --}}
                <div class="form-group col-md-12">
                    <label class="form-label">
                        <i class="fa fa-map-marker-alt mr-1 text-muted"></i> {{ __('Location') }}
                    </label>

                    <input type="text"
                           name="location"
                           class="form-control @error('location') is-invalid @enderror"
                           value="{{ old('location', $warehouse->location) }}"
                           placeholder="{{ __('Warehouse location') }}">

                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

            </div>

        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('admin.warehouses.index') }}"
               class="btn btn-outline-secondary mr-2">
                <i class="fa fa-times"></i> {{ __('Cancel') }}
            </a>

            <button type="submit" class="btn btn-primary custom-btn">
                <i class="fa fa-save mr-1"></i> {{ __('Update Warehouse') }}
            </button>
        </div>

    </div>

</form>

@stop

@extends('adminlte::page')

@section('title', __('Adjust Stock') . ': ' . $stock->item->name)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-sliders-h text-danger"></i>
        {{ __('Adjust / Waste Stock') }}
    </h1>

    <a href="{{ route('admin.inventory.show', $stock->id) }}" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left"></i> {{ __('Back to Stock') }}
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
        @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>

    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif


<div class="card shadow-sm">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fa fa-box-open mr-1 text-muted"></i>
            {{ __('Item Information') }}
        </h3>
    </div>

    <div class="card-body">

        <p class="text-muted">
            <strong>{{ __('Item') }}:</strong> {{ $stock->item->name }} <br>
            <strong>{{ __('Warehouse') }}:</strong> {{ $stock->warehouse->name ?? 'Default' }} <br>
            <strong>{{ __('Current Stock') }}:</strong> {{ $stock->quantity }} {{ $stock->unit_type }}
        </p>

        <hr>

        <form action="{{ route('admin.inventory.adjust', $stock->id) }}" method="POST">
            @csrf

            <div class="form-row">

                {{-- Quantity --}}
                <div class="form-group col-md-6">
                    <label class="form-label">
                        <i class="fa fa-sort-numeric-down mr-1 text-muted"></i>
                        {{ __('Adjustment Quantity') }}
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           step="0.001"
                           name="quantity"
                           class="form-control @error('quantity') is-invalid @enderror"
                           placeholder="{{ __('Enter quantity to deduct') }}"
                           required>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Type --}}
                <div class="form-group col-md-6">
                    <label class="form-label">
                        <i class="fa fa-tags mr-1 text-muted"></i>
                        {{ __('Adjustment Type') }}
                        <span class="text-danger">*</span>
                    </label>

                    <select name="type"
                            class="form-control @error('type') is-invalid @enderror"
                            required>
                        <option value="WASTE">{{ __('Waste') }}</option>
                        <option value="ADJUSTMENT">{{ __('Adjustment') }}</option>
                    </select>

                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Cause --}}
            <div class="form-group">
                <label class="form-label">
                    <i class="fa fa-comment-alt mr-1 text-muted"></i>
                    {{ __('Cause / Note (optional)') }}
                </label>
                <input type="text"
                       name="cause"
                       class="form-control"
                       placeholder="{{ __('Reason for adjustment') }}">
            </div>


            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('admin.inventory.show', $stock->id) }}"
                   class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-times"></i> {{ __('Cancel') }}
                </a>

                <button type="submit" class="btn btn-danger custom-btn">
                    <i class="fa fa-minus-circle mr-1"></i> {{ __('Apply Adjustment') }}
                </button>
            </div>

        </form>

    </div>
</div>

@stop

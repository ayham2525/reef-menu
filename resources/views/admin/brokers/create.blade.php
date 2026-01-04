@extends('adminlte::page')

@section('title', __('Create Broker'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-user-tie text-primary"></i> {{ __('Create Broker') }}
        </h1>
        <a href="{{ route('admin.brokers.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back to Brokers') }}
        </a>
    </div>
@stop

@section('content')
    {{-- Validation Errors --}}
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

    <form action="{{ route('admin.brokers.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fa fa-info-circle mr-1 text-muted"></i> {{ __('Broker Details') }}</h3>
            </div>

            <div class="card-body">
                <div class="form-row">
                    {{-- Agency --}}
                    <div class="form-group col-md-6">
                        <label for="agency_id"><i class="fa fa-building mr-1 text-muted"></i> {{ __('Agency') }}</label>
                        <select name="agency_id" id="agency_id" class="form-control @error('agency_id') is-invalid @enderror">
                            <option value="">{{ __('Select agency (optional)') }}</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ old('agency_id') == $agency->id ? 'selected' : '' }}>
                                    {{ $agency->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('agency_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Name --}}
                    <div class="form-group col-md-6">
                        <label for="name"><i class="fa fa-user mr-1 text-muted"></i> {{ __('Name') }} <span class="text-danger">*</span></label>
                        <input type="text"
                               id="name"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="{{ __('Enter full name') }}"
                               required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row">
                    {{-- Email --}}
                    <div class="form-group col-md-6">
                        <label for="email"><i class="fa fa-envelope mr-1 text-muted"></i> {{ __('Email') }}</label>
                        <input type="email"
                               id="email"
                               name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="name@example.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="form-group col-md-6">
                        <label for="phone"><i class="fa fa-phone mr-1 text-muted"></i> {{ __('Phone') }}</label>
                        <input type="text"
                               id="phone"
                               name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}"
                               placeholder="+9715xxxxxxxx">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row">
                    {{-- BRN --}}
                    <div class="form-group col-md-6">
                        <label for="brn"><i class="fa fa-id-badge mr-1 text-muted"></i> {{ __('BRN') }}</label>
                        <input type="text"
                               id="brn"
                               name="brn"
                               class="form-control @error('brn') is-invalid @enderror"
                               value="{{ old('brn') }}"
                               placeholder="{{ __('Broker Registration Number') }}">
                        @error('brn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="form-group col-md-6">
                        <label for="is_active"><i class="fa fa-toggle-on mr-1 text-muted"></i> {{ __('Status') }}</label>
                        <select id="is_active" name="is_active" class="form-control @error('is_active') is-invalid @enderror">
                            <option value="1" @selected(old('is_active', 1)==1)>{{ __('Active') }}</option>
                            <option value="0" @selected(old('is_active', 1)==0)>{{ __('Inactive') }}</option>
                        </select>
                        @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.brokers.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-times"></i> {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary custom-btn">
                    <i class="fa fa-save mr-1"></i> {{ __('Save') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
$(document).ready(function () {
    $('#agency_id').select2({
        width: '100%',
        placeholder: "{{ __('Select agency (optional)') }}"
    });
});
</script>
@stop

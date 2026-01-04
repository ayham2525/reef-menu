@extends('adminlte::page')

@section('title', __('Edit Vendor'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-truck-loading text-primary"></i> {{ __('Edit Vendor') }}
        </h1>
        <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back to Vendors') }}
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

    <form action="{{ route('admin.vendors.update', $vendor->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fa fa-info-circle mr-1 text-muted"></i> {{ __('Vendor Details') }}
                </h3>
            </div>

            <div class="card-body">

                <div class="form-row">

                    {{-- Vendor Name --}}
                    <div class="form-group col-md-6">
                        <label for="name">
                            <i class="fa fa-user mr-1 text-muted"></i> {{ __('Vendor Name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               name="name"
                               id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $vendor->name) }}"
                               placeholder="{{ __('Enter vendor name') }}"
                               required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Contact Person --}}
                    <div class="form-group col-md-6">
                        <label for="contact_person">
                            <i class="fa fa-user-circle mr-1 text-muted"></i> {{ __('Contact Person') }}
                        </label>
                        <input type="text"
                               name="contact_person"
                               id="contact_person"
                               class="form-control @error('contact_person') is-invalid @enderror"
                               value="{{ old('contact_person', $vendor->contact_person) }}"
                               placeholder="{{ __('Full name of contact person') }}">
                        @error('contact_person') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>

                <div class="form-row">

                    {{-- Phone --}}
                    <div class="form-group col-md-6">
                        <label for="phone">
                            <i class="fa fa-phone mr-1 text-muted"></i> {{ __('Phone') }}
                        </label>
                        <input type="text"
                               name="phone"
                               id="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $vendor->phone) }}"
                               placeholder="+9715xxxxxxxx">
                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group col-md-6">
                        <label for="email">
                            <i class="fa fa-envelope mr-1 text-muted"></i> {{ __('Email') }}
                        </label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $vendor->email) }}"
                               placeholder="vendor@example.com">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>

                {{-- Address --}}
                <div class="form-group">
                    <label for="address">
                        <i class="fa fa-map-marker-alt mr-1 text-muted"></i> {{ __('Address') }}
                    </label>
                    <textarea name="address"
                              id="address"
                              rows="3"
                              class="form-control @error('address') is-invalid @enderror"
                              placeholder="{{ __('Vendor full address') }}">{{ old('address', $vendor->address) }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div class="form-group col-md-4 pl-0">
                    <label for="is_active">
                        <i class="fa fa-toggle-on mr-1 text-muted"></i> {{ __('Status') }}
                    </label>
                    <select class="form-control" id="is_active" name="is_active">
                        <option value="1" @selected(old('is_active', $vendor->is_active)==1)>
                            {{ __('Active') }}
                        </option>
                        <option value="0" @selected(old('is_active', $vendor->is_active)==0)>
                            {{ __('Inactive') }}
                        </option>
                    </select>
                </div>

            </div>

            <div class="card-footer d-flex justify-content-end">
                <a href="{{ route('admin.vendors.index') }}" class="btn btn-outline-secondary mr-2">
                    <i class="fa fa-times"></i> {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary custom-btn">
                    <i class="fa fa-save mr-1"></i> {{ __('Update Vendor') }}
                </button>
            </div>
        </div>
    </form>

@stop

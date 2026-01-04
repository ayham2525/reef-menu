@extends('adminlte::page')

@section('title', $agency->name)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-building text-primary"></i> {{ $agency->name }}
        </h1>
        <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back to Agencies') }}
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header"><h3 class="card-title">{{ __('Agency Details') }}</h3></div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3"><i class="fa fa-code text-muted"></i> {{ __('Code') }}</dt>
                <dd class="col-sm-9">{{ $agency->code }}</dd>

                <dt class="col-sm-3"><i class="fa fa-id-card text-muted"></i> {{ __('License No.') }}</dt>
                <dd class="col-sm-9">{{ $agency->license_no ?? '—' }}</dd>

                <dt class="col-sm-3"><i class="fa fa-envelope text-muted"></i> {{ __('Email') }}</dt>
                <dd class="col-sm-9">{{ $agency->email ?? '—' }}</dd>

                <dt class="col-sm-3"><i class="fa fa-phone text-muted"></i> {{ __('Phone') }}</dt>
                <dd class="col-sm-9">{{ $agency->phone ?? '—' }}</dd>

                <dt class="col-sm-3"><i class="fa fa-toggle-on text-muted"></i> {{ __('Status') }}</dt>
                <dd class="col-sm-9">
                    @if($agency->is_active)
                        <span class="badge badge-success">
                            <i class="fa fa-check-circle"></i> {{ __('Active') }}
                        </span>
                    @else
                        <span class="badge badge-secondary">
                            <i class="fa fa-ban"></i> {{ __('Inactive') }}
                        </span>
                    @endif
                </dd>
            </dl>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <a href="{{ route('admin.agencies.edit', $agency) }}" class="btn btn-primary custom-btn mr-2">
                <i class="fa fa-pen mr-1"></i> {{ __('Edit') }}
            </a>
            <a href="{{ route('admin.agencies.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left mr-1"></i> {{ __('Back') }}
            </a>
        </div>
    </div>
@stop

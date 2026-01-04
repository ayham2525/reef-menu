@extends('adminlte::page')

@section('title', __('Purchase Orders'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-file-invoice-dollar text-primary"></i> {{ __('Purchase Orders') }}
    </h1>

    <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary custom-btn">
        <i class="fa fa-plus mr-1"></i> {{ __('New Purchase Order') }}
    </a>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa fa-check-circle mr-1"></i>{{ session('success') }}

        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

<div class="card shadow-sm">

    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="fa fa-list-ul text-muted mr-1"></i>
            {{ __('Purchase Orders List') }}
        </h3>
    </div>

    <div class="card-body">

        {{-- Filters --}}
        <form id="filterForm" class="mb-3">
            <div class="form-row">
                <div class="col-md-4 mb-2">
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="{{ __('Search by code or vendor...') }}"
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3 mb-2">
                    <select name="status" id="status" class="form-control">
                        <option value="all">{{ __('All Statuses') }}</option>
                        <option value="draft">{{ __('Draft') }}</option>
                        <option value="received">{{ __('Received') }}</option>
                    </select>
                </div>
            </div>
        </form>

        {{-- Table container (AJAX reload) --}}
        <div id="table-container">
            @include('admin.purchase-orders.partials.table', ['orders' => $orders])
        </div>

    </div>
</div>

@stop

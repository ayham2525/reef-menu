@extends('adminlte::page')

@section('title', 'Warehouse Transfers')

@section('content_header')
    <h1><i class="fa fa-exchange-alt text-primary"></i> Warehouse Transfers</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Transfers List</h3>
        <a href="{{ route('admin.inventory.transfers.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Transfer
        </a>
    </div>

    <div class="card-body p-0">
        <table class="table table-bordered table-striped mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>From</th>
                    <th>To</th</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Approved By</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($transfers as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>{{ $t->fromWarehouse->name }}</td>
                    <td>{{ $t->toWarehouse->name }}</td>

                    <td>
                        @if($t->status == 'approved')
                            <span class="badge badge-success">Approved</span>
                        @else
                            <span class="badge badge-warning">Draft</span>
                        @endif
                    </td>

                    <td>{{ $t->creator->name ?? '-' }}</td>
                    <td>{{ $t->approver->name ?? '-' }}</td>

                    <td class="text-center">
                        <a href="{{ route('admin.inventory.transfers.show', $t->id) }}"
                           class="text-primary" title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('admin.inventory.transfers.pdf', $t->id) }}"
                           class="text-danger ml-2">
                            <i class="fa fa-file-pdf"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>

        <div class="p-3">
            {{ $transfers->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

@stop

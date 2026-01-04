@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Users</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary custom-btn">
            <i class="fas fa-plus"></i> Add User
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 90px;">Admin</th>
                        <th style="width: 160px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        // start number based on pagination
                        $i = ($users->currentPage() - 1) * $users->perPage();
                    @endphp

                    @forelse($users as $u)
                        @php $i++; @endphp
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @if($u->is_admin)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                           <td class="text-nowrap">
    {{-- Edit --}}
    <a href="{{ route('admin.users.edit', $u) }}"
       class="btn btn-sm btn-info"
       title="Edit">
        <i class="fas fa-edit"></i>
    </a>

    {{-- Delete --}}
    <form action="{{ route('admin.users.destroy', $u) }}"
          method="POST"
          class="d-inline"
          onsubmit="return confirm('Delete this user?')">
        @csrf
        @method('DELETE')

        <button type="submit"
                class="btn btn-sm btn-danger"
                title="Delete">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="card-footer">
                {{-- Bootstrap 4 pagination --}}
                {{ $users->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
@stop

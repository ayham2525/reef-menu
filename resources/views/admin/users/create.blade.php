@extends('adminlte::page')

@section('title', 'Add User')

@section('content_header')
    <h1>Add User</h1>
@stop

@section('content')
    <div class="card">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="card-body">

                <div class="form-group">
                    <label>Name</label>
                    <input name="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input name="password" type="password" class="form-control" required>
                    @error('password')<small class="text-danger">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input name="password_confirmation" type="password" class="form-control" required>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" value="1">
                    <label class="form-check-label" for="is_admin">Admin</label>
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
                <button class="btn btn-primary custom-btn">Save</button>
            </div>
        </form>
    </div>
@stop

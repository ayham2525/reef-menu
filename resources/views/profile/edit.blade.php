@extends('adminlte::page')

@section('title', __('My Profile'))

@section('content_header')
    <h1><i class="fa fa-user text-primary"></i> {{ __('My Profile') }}</h1>
@stop

@section('content')

    {{-- Flash Messages --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">
            <i class="fa fa-check-circle mr-1"></i> {{ __('Profile updated successfully.') }}
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success">
            <i class="fa fa-check-circle mr-1"></i> {{ __('Password updated successfully.') }}
        </div>
    @endif

    @if (session('status') === 'verification-link-sent')
        <div class="alert alert-info">
            <i class="fa fa-envelope mr-1"></i> {{ __('A new verification link has been sent to your email.') }}
        </div>
    @endif

    <div class="row">

        {{-- ===================== PROFILE INFO ===================== --}}
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <i class="fa fa-id-card mr-1"></i> {{ __('Profile Information') }}
                </div>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="card-body">

                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', auth()->user()->name) }}"
                                required
                                autofocus
                            >
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', auth()->user()->email) }}"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email Verification Notice (if your User implements MustVerifyEmail) --}}
                        @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                            <div class="alert alert-warning mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <i class="fa fa-exclamation-triangle mr-1"></i>
                                        {{ __('Your email address is not verified.') }}
                                    </div>

                                    <form method="POST" action="{{ route('verification.send') }}" class="mt-2 mt-sm-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-dark">
                                            <i class="fa fa-paper-plane mr-1"></i> {{ __('Resend Verification Email') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i> {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== UPDATE PASSWORD ===================== --}}
        <div class="col-md-6" id="update-password">
            <div class="card card-info">
                <div class="card-header">
                    <i class="fa fa-key mr-1"></i> {{ __('Change Password') }}
                </div>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        <div class="form-group">
                            <label for="current_password">{{ __('Current Password') }}</label>
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                class="form-control @error('current_password') is-invalid @enderror"
                                required
                                autocomplete="current-password"
                            >
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('New Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required
                                autocomplete="new-password"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="password_confirmation">{{ __('Confirm New Password') }}</label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                class="form-control"
                                required
                                autocomplete="new-password"
                            >
                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-info">
                            <i class="fa fa-lock mr-1"></i> {{ __('Update Password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== DELETE ACCOUNT ===================== --}}
        <div class="col-12">
            <div class="card card-danger">
                <div class="card-header">
                    <i class="fa fa-trash mr-1"></i> {{ __('Delete Account') }}
                </div>

                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')

                    <div class="card-body">
                        <p class="text-muted mb-3">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="form-group">
                            <label for="delete_password">{{ __('Password') }}</label>
                            <input
                                id="delete_password"
                                name="password"
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="{{ __('Enter your password') }}"
                                required
                                autocomplete="current-password"
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">
                            <i class="fa fa-trash mr-1"></i> {{ __('Delete Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@stop

@push('js')
<script>
    function confirmDeleteAccount() {
        if (confirm("{{ __('Are you sure you want to delete your account? This action cannot be undone.') }}")) {
            document.getElementById('deleteAccountForm').submit();
        }
    }
</script>
@endpush

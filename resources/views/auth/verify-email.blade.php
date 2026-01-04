@extends('adminlte::page')

@section('title', __('Verify Email'))

@section('content_header')
    <h1><i class="fa fa-envelope text-primary"></i> {{ __('Verify Email') }}</h1>
@stop

@section('content')

    <div class="card">
        <div class="card-body">

            <p class="text-muted mb-3">
                {{ __("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success">
                    <i class="fa fa-check-circle mr-1"></i>
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </div>
            @endif

            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <form method="POST" action="{{ route('verification.send') }}" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-paper-plane mr-1"></i> {{ __('Resend Verification Email') }}
                    </button>
                </form>

                <div class="mb-2">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-user mr-1"></i> {{ __('View Profile') }}
                    </a>

                    <a href="{{ route('profile.edit') . '#update-password' }}" class="btn btn-outline-info ml-1">
                        <i class="fa fa-key mr-1"></i> {{ __('Change Password') }}
                    </a>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="mb-2">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fa fa-sign-out-alt mr-1"></i> {{ __('Log Out') }}
                    </button>
                </form>

            </div>

        </div>
    </div>

@stop

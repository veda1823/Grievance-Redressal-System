@extends('layouts.app')

@section('title', 'Staff Login')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Staff Login</h1>
        <p>Sign in to review and manage student complaints</p>
    </div>

    <form method="POST" action="{{ route('staff.login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Staff Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="staff@example.com"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" id="remember" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember" class="form-check-label">Remember me on this device</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
            Log In
        </button>
    </form>

    <div class="auth-footer">
        Staff accounts are provisioned by an administrator.
        <br>
        Student? <a href="{{ route('login') }}">Student Login</a>
    </div>
</div>
@endsection

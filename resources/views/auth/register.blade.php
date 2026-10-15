@extends('layouts.app')

@section('title', 'Student Registration')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h1>Student Registration</h1>
        <p>Create your account to submit and track grievances</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Full Name</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                required
                autofocus
                placeholder="Enter your full name"
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                placeholder="student@example.com"
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
                placeholder="At least 8 characters"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
                class="form-control"
                required
                placeholder="Confirm your password"
            >
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">
            Register as Student
        </button>
    </form>

    <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}">Log in here</a>
    </div>
</div>
@endsection

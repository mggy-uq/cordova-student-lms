@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <div class="form-heading">
        <p class="eyebrow">STUDENT PORTAL</p>
        <h2>Welcome back</h2>
        <p>Sign in to continue to your learning space.</p>
    </div>

    <form class="auth-form" method="POST" action="{{ route('login.store') }}">
        @csrf

        <div class="form-field">
            <label for="email">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="username"
                inputmode="email"
                required
                autofocus
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @if ($errors->has('email')) aria-describedby="email-error" @endif
                class="@error('email') input-invalid @enderror"
            >
            @error('email')
                <p class="field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <div class="label-row">
                <label for="password">Password</label>
            </div>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                @if ($errors->has('password')) aria-describedby="password-error" @endif
                class="@error('password') input-invalid @enderror"
            >
            @error('password')
                <p class="field-error" id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <label class="remember-option" for="remember">
            <input id="remember" name="remember" type="checkbox" value="1">
            <span>Remember me</span>
        </label>

        <button class="primary-button" type="submit">
            <span>Sign in</span>
            <span class="button-arrow" aria-hidden="true">&rarr;</span>
        </button>
    </form>

    <p class="form-switch">New to the portal? <a href="{{ route('register') }}">Create an account</a></p>
@endsection

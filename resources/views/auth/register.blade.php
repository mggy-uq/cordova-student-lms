@extends('layouts.auth')

@section('title', 'Create account')

@section('content')
    <div class="form-heading">
        <p class="eyebrow">JOIN YOUR CAMPUS</p>
        <h2>Create your account</h2>
        <p>Set up your student portal access to get started.</p>
    </div>

    <form class="auth-form" method="POST" action="{{ route('register.store') }}">
        @csrf

        <div class="form-field">
            <label for="name">Full name</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                autocomplete="name"
                maxlength="255"
                required
                autofocus
                aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                @if ($errors->has('name')) aria-describedby="name-error" @endif
                class="@error('name') input-invalid @enderror"
            >
            @error('name')
                <p class="field-error" id="name-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="email">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                autocomplete="email"
                inputmode="email"
                maxlength="255"
                required
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                @if ($errors->has('email')) aria-describedby="email-error" @endif
                class="@error('email') input-invalid @enderror"
            >
            @error('email')
                <p class="field-error" id="email-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password">Password</label>
            <input
                id="password"
                name="password"
                type="password"
                autocomplete="new-password"
                minlength="8"
                required
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                @if ($errors->has('password')) aria-describedby="password-error" @endif
                class="@error('password') input-invalid @enderror"
            >
            @error('password')
                <p class="field-error" id="password-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-field">
            <label for="password_confirmation">Confirm password</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                minlength="8"
                required
            >
        </div>

        <button class="primary-button" type="submit">
            <span>Create account</span>
            <span class="button-arrow" aria-hidden="true">&rarr;</span>
        </button>
    </form>

    <p class="form-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
@endsection

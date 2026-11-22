@extends('layouts.dashboard')

@section('title', 'Settings')
@section('page-heading', 'Settings')

@section('content')
    <div class="dashboard-content settings-page">
        <section class="settings-page-heading" aria-labelledby="settings-page-title">
            <div>
                <p class="welcome-eyebrow">ACCOUNT MANAGEMENT</p>
                <h2 id="settings-page-title">Profile &amp; Settings</h2>
                <p>Update your profile information and account password.</p>
            </div>
        </section>

        @if (session('profile_status'))
            <div class="settings-alert" role="status">{{ session('profile_status') }}</div>
        @endif
        @if (session('password_status'))
            <div class="settings-alert" role="status">{{ session('password_status') }}</div>
        @endif

        <div class="settings-grid">
            <section class="settings-card" aria-labelledby="profile-heading">
                <div class="settings-card-heading">
                    <span class="settings-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="3.5"/><path d="M5.5 20a6.5 6.5 0 0 1 13 0"/></svg>
                    </span>
                    <div>
                        <h3 id="profile-heading">Profile information</h3>
                        <p>Update your name and email address.</p>
                    </div>
                </div>

                <form class="settings-form" method="POST" action="{{ route('settings.profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="settings-field">
                        <label for="name">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $student->name) }}" required maxlength="255">
                        @error('name')
                            <p class="settings-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="settings-field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $student->email) }}" required maxlength="255">
                        @error('email')
                            <p class="settings-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit">Save profile</button>
                </form>
            </section>

            <section class="settings-card" aria-labelledby="password-heading">
                <div class="settings-card-heading">
                    <span class="settings-card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v3"/></svg>
                    </span>
                    <div>
                        <h3 id="password-heading">Change password</h3>
                        <p>Use at least 8 characters.</p>
                    </div>
                </div>

                <form class="settings-form" method="POST" action="{{ route('settings.password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="settings-field">
                        <label for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
                        @error('current_password')
                            <p class="settings-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="settings-field">
                        <label for="password">New password</label>
                        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                        @error('password')
                            <p class="settings-field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="settings-field">
                        <label for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
                    </div>
                    <button type="submit">Change password</button>
                </form>
            </section>
        </div>
    </div>
@endsection

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123452">
    <title>@yield('title') | Cordova Public College</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    <main class="auth-shell">
        <aside class="welcome-panel">
            <a class="brand" href="{{ route('login') }}" aria-label="Cordova Public College Student Portal">
                <span class="brand-mark" aria-hidden="true">C</span>
                <span class="brand-copy">
                    <strong>Cordova Public College</strong>
                    <small>Student Learning Portal</small>
                </span>
            </a>

            <div class="welcome-message">
                <p class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</p>
                <h1>Make every day<br> a step forward.</h1>
                <p class="welcome-description">One place for your classes, learning resources, and progress at Cordova Public College.</p>
            </div>

            <p class="welcome-footer"><span class="status-dot" aria-hidden="true"></span>Learn with purpose. Grow with confidence.</p>
            <div class="panel-orbit panel-orbit-large" aria-hidden="true"></div>
            <div class="panel-orbit panel-orbit-small" aria-hidden="true"></div>
        </aside>

        <section class="auth-main">
            <div class="mobile-brand" aria-label="Cordova Public College">
                <span class="brand-mark" aria-hidden="true">C</span>
                <span class="brand-copy">
                    <strong>Cordova Public College</strong>
                    <small>Student Learning Portal</small>
                </span>
            </div>

            <div class="auth-content">
                @yield('content')
            </div>

            <p class="copyright">Cordova Public College <span aria-hidden="true">&middot;</span> Student portal</p>
        </section>
    </main>
</body>
</html>

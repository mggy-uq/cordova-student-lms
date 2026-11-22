<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123452">
    <title>@yield('title', 'Dashboard') | Student LMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body class="dashboard-body">
    <button class="sidebar-overlay" type="button" aria-label="Close navigation" tabindex="-1"></button>

    <aside class="sidebar" id="student-sidebar">
        <a class="dashboard-brand" href="{{ route('dashboard') }}">
            <span class="dashboard-brand-mark" aria-hidden="true">C</span>
            <span class="dashboard-brand-copy">
                <strong>Cordova Public College</strong>
                <small>Student Learning Management System</small>
            </span>
        </a>

        <p class="sidebar-label">MENU</p>
        <nav class="sidebar-nav" aria-label="Student navigation">
            <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>
                <span>Dashboard</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('courses.index') ? 'is-active' : '' }}" href="{{ route('courses.index') }}" @if(request()->routeIs('courses.index')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15H6.5A2.5 2.5 0 0 0 4 20.5z"/><path d="M4 5.5v15M8 7h8M8 11h7"/></svg>
                <span>My Courses</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('assignments.*') ? 'is-active' : '' }}" href="{{ route('assignments.index') }}" @if(request()->routeIs('assignments.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 4h8M9 3h6v3H9z"/><path d="M6 4H4v17h16V4h-2M8 11l2 2 5-5M8 17h8"/></svg>
                <span>Assignments</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('grades.*') ? 'is-active' : '' }}" href="{{ route('grades.index') }}" @if(request()->routeIs('grades.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 14.8 8.7 21 9.6l-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9z"/></svg>
                <span>Grades</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('announcements.*') ? 'is-active' : '' }}" href="{{ route('announcements.index') }}" @if(request()->routeIs('announcements.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 14V9a2 2 0 0 1 2-2h3l8-3v15l-8-3H6a2 2 0 0 1-2-2z"/><path d="m8 16 1.5 5h4L11 16M20 9l2-1M20 14l2 1"/></svg>
                <span>Announcements</span>
            </a>
            <a class="sidebar-link {{ request()->routeIs('settings.*') ? 'is-active' : '' }}" href="{{ route('settings.edit') }}" @if(request()->routeIs('settings.*')) aria-current="page" @endif>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7z"/><path d="M19 12a7 7 0 0 0-.1-1l2-1.5-2-3.4-2.4 1a7 7 0 0 0-1.7-1L14.5 3h-5L9.2 6.1a7 7 0 0 0-1.7 1l-2.4-1-2 3.4 2 1.5a7 7 0 0 0 0 2l-2 1.5 2 3.4 2.4-1a7 7 0 0 0 1.7 1l.3 3.1h5l.3-3.1a7 7 0 0 0 1.7-1l2.4 1 2-3.4-2-1.5a7 7 0 0 0 .1-1z"/></svg>
                <span>Settings</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="student-profile">
                <span class="student-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                <span class="student-profile-copy">
                    <strong>{{ $student->name }}</strong>
                    <small>{{ $student->email }}</small>
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-link logout-link" type="submit">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5M14 16l4-4-4-4M18 12H9"/></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="topbar">
            <button class="menu-toggle" type="button" aria-label="Open navigation" aria-controls="student-sidebar" aria-expanded="false">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="topbar-title">
                <p>STUDENT PORTAL</p>
                <h1>@yield('page-heading', 'Dashboard')</h1>
            </div>
            <div class="topbar-date">{{ now()->format('D, M j') }}</div>
        </header>

        @yield('content')

        <footer class="dashboard-footer">Cordova Public College <span aria-hidden="true">&middot;</span> Student LMS</footer>
    </main>

    <script src="{{ asset('js/dashboard.js') }}" defer></script>
</body>
</html>

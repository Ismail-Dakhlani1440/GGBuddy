<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — GGBuddy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --bg:           #0b0c16;
            --surface:      #13141f;
            --surface2:     #1a1c2e;
            --surface3:     #22243a;
            --border:       rgba(255,255,255,0.07);
            --border-2:     rgba(255,255,255,0.12);
            --accent:       #7c3aed;
            --accent-light: #9d5ff5;
            --text:         #ffffff;
            --text-2:       #9ca3af;
            --text-3:       #4b5563;
            --red:          #ef4444;
            --green:        #22c55e;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }

        .top-nav {
            position: fixed; top: 0; left: 0; right: 0;
            background: rgba(11,12,22,0.94); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border); z-index: 200;
            display: flex; align-items: center; height: 60px; padding: 0 32px;
        }

        .logo {
            font-size: 18px; font-weight: 900; letter-spacing: -0.04em;
            background: linear-gradient(135deg, #fff 30%, #9d5ff5 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none; margin-right: 32px;
        }

        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-link {
            padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500;
            color: var(--text-2); text-decoration: none; transition: all 0.15s;
        }
        .nav-link:hover { color: var(--text); background: rgba(255,255,255,0.05); }
        .nav-link.active { color: var(--text); font-weight: 600; background: rgba(124,58,237,0.12); }

        .nav-right { margin-left: auto; display: flex; align-items: center; gap: 16px; }

        .page-wrapper { padding-top: 60px; min-height: 100vh; }
        .page-content { max-width: 1100px; margin: 0 auto; padding: 40px 32px; }

        .btn {
            padding: 8px 16px; border-radius: 100px; font-size: 13px; font-weight: 700;
            text-decoration: none; cursor: pointer; transition: all 0.15s; border: none;
            font-family: 'Inter', sans-serif;
        }
        .btn-ghost { background: var(--surface2); color: var(--text); border: 1px solid var(--border-2); }
        .btn-ghost:hover { background: var(--surface3); }
        .btn-primary { background: var(--accent); color: white; }

        .flash-zone { max-width: 1100px; margin: 0 auto; padding: 20px 32px 0; }
        .flash { padding: 12px 16px; border-radius: 12px; font-size: 13px; font-weight: 500; border: 1px solid transparent; }
        .flash-success { background: rgba(34,197,94,0.08); border-color: rgba(34,197,94,0.2); color: var(--green); }
        .flash-error { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.2); color: var(--red); }
    </style>
</head>
<body>
    <nav class="top-nav">
        <a href="{{ route('admin.dashboard') }}" class="logo">GGBuddy Admin</a>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Overview</a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">Reports</a>
            <a href="{{ route('admin.ebuddies.index') }}" class="nav-link {{ request()->routeIs('admin.ebuddies.*') ? 'active' : '' }}">Applications</a>
        </div>
        <div class="nav-right">
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost">Sign Out</button>
            </form>
        </div>
    </nav>

    <div class="page-wrapper">
        @if(session('success') || session('error'))
        <div class="flash-zone">
            @if(session('success')) <div class="flash flash-success">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="flash flash-error">{{ session('error') }}</div> @endif
        </div>
        @endif

        <div class="page-content">
            @yield('content')
        </div>
    </div>
</body>
</html>

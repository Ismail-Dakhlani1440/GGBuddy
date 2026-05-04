<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} — GGBuddy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            --accent-bg:    rgba(124,58,237,0.12);
            --text:         #ffffff;
            --text-2:       #9ca3af;
            --text-3:       #4b5563;
            --green:        #22c55e;
            --yellow:       #f59e0b;
            --red:          #ef4444;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { height: 100%; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

        /* ─── Nav ─── */
        .top-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: rgba(11,12,22,0.94);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            z-index: 200;
            display: flex;
            align-items: center;
            height: 60px;
            padding: 0 32px;
            gap: 0;
        }
        .logo {
            font-size: 18px; font-weight: 900; letter-spacing: -0.04em;
            background: linear-gradient(135deg, #fff 30%, #9d5ff5 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none; flex-shrink: 0; margin-right: 20px;
        }
        .nav-links { display: flex; align-items: center; gap: 2px; flex: 1; }
        .nav-link {
            padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500;
            color: var(--text-2); text-decoration: none; transition: all 0.15s; white-space: nowrap;
        }
        .nav-link:hover  { color: var(--text); background: rgba(255,255,255,0.05); }
        .nav-link.active { color: var(--text); font-weight: 600; background: rgba(124,58,237,0.12); }
        .nav-right { display: flex; align-items: center; gap: 10px; margin-left: auto; flex-shrink: 0; }
        .nav-avatar {
            width: 34px; height: 34px; border-radius: 50%; overflow: hidden;
            border: 2px solid rgba(124,58,237,0.4); transition: border-color 0.2s; cursor: pointer; text-decoration: none; flex-shrink: 0;
        }
        .nav-avatar:hover { border-color: var(--accent-light); }
        .nav-avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* ─── Page ─── */
        .page-wrapper  { padding-top: 60px; min-height: 100vh; }
        .flash-zone    { max-width: 1100px; margin: 0 auto; padding: 20px 24px 0; }
        .page-content  { max-width: 1100px; margin: 0 auto; padding: 32px 24px 80px; }

        /* ─── Cards ─── */
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; transition: border-color 0.2s; }
        .card:hover { border-color: rgba(124,58,237,0.2); }

        /* ─── Buttons ─── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 24px; border-radius: 100px; font-size: 13px; font-weight: 700;
            text-decoration: none; cursor: pointer; transition: all 0.15s; border: none;
            font-family: 'Inter', sans-serif; letter-spacing: 0.01em; white-space: nowrap; line-height: 1;
        }
        .btn-primary { background: var(--accent); color: #fff; box-shadow: 0 4px 16px rgba(124,58,237,0.3); }
        .btn-primary:hover { background: var(--accent-light); transform: translateY(-1px); }
        .btn-ghost { background: var(--surface2); color: var(--text); border: 1px solid var(--border-2); }
        .btn-ghost:hover { background: var(--surface3); }
        .btn-danger { background: rgba(239,68,68,0.1); color: var(--red); border: 1px solid rgba(239,68,68,0.15); }
        .btn-danger:hover { background: rgba(239,68,68,0.2); }
        .btn-sm { padding: 8px 20px; font-size: 12px; }

        /* ─── Forms ─── */
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-label { font-size: 12px; font-weight: 600; color: var(--text-2); letter-spacing: 0.02em; }
        .form-input {
            width: 100%; padding: 11px 16px; background: var(--surface2); border: 1px solid var(--border-2);
            border-radius: 12px; color: var(--text); font-size: 14px; font-family: 'Inter', sans-serif;
            outline: none; transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(124,58,237,0.15); }
        .form-input::placeholder { color: var(--text-3); }
        textarea.form-input { resize: vertical; }

        /* ─── Badges ─── */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 12px; border-radius: 100px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em; }
        .badge-purple { background: rgba(124,58,237,0.15); color: #c084fc; border: 1px solid rgba(124,58,237,0.25); }
        .badge-green  { background: rgba(34,197,94,0.1); color: var(--green); border: 1px solid rgba(34,197,94,0.2); }
        .badge-yellow { background: rgba(245,158,11,0.1); color: var(--yellow); border: 1px solid rgba(245,158,11,0.2); }

        /* ─── Flash ─── */
        .flash { padding: 14px 20px; border-radius: 12px; font-size: 13px; font-weight: 500; }
        .flash-success { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); color: var(--green); }
        .flash-error   { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: var(--red); }

        /* ─── Helpers ─── */
        .section-title { font-size: 15px; font-weight: 700; color: var(--text); }
        .section-sub   { font-size: 13px; color: var(--text-2); margin-top: 3px; }
        .side-col { display: grid; grid-template-columns: 1fr 260px; gap: 20px; align-items: start; }
        .two-col  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface3); border-radius: 10px; }
        [x-cloak] { display: none !important; }

        /* ─── Responsive ─── */
        @media (max-width: 900px) {
            .side-col { grid-template-columns: 1fr; }
            .two-col  { grid-template-columns: 1fr; }
            .page-content { padding: 24px 16px 60px; }
            .flash-zone   { padding: 14px 16px 0; }
        }

        @media (max-width: 640px) {
            .top-nav {
                height: auto;
                flex-wrap: wrap;
                padding: 0 16px;
                gap: 0;
                align-items: center;
            }
            .logo    { order: 1; height: 56px; display: flex; align-items: center; margin-right: 0; }
            .nav-right { order: 2; margin-left: auto; height: 56px; align-items: center; }
            .nav-links {
                order: 3; width: 100%; flex: none;
                overflow-x: auto; overflow-y: hidden;
                -webkit-overflow-scrolling: touch; scrollbar-width: none;
                padding: 4px 0 10px; gap: 2px;
                border-top: 1px solid var(--border); align-items: center;
            }
            .nav-links::-webkit-scrollbar { display: none; }
            .nav-link { padding: 6px 12px; font-size: 12px; }
            .nav-online-label { display: none; }
            .page-wrapper { padding-top: 96px; }
            .page-content { padding: 20px 14px 60px; }
            .flash-zone   { padding: 12px 14px 0; }
            .btn { padding: 9px 20px; }
            .btn-sm { padding: 7px 16px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <nav class="top-nav">
        <a href="{{ route('browse.index') }}" class="logo">GGBuddy</a>
        <div class="nav-links">
            <a href="{{ route('browse.index') }}" class="nav-link {{ request()->routeIs('browse.index','player.dashboard') ? 'active' : '' }}">Find E-Buddy</a>
            <a href="{{ route('browse.my-orders') }}" class="nav-link {{ request()->routeIs('browse.my-orders') ? 'active' : '' }}">My Orders</a>
        </div>
        <div class="nav-right">

            <div class="nav-avatar">
                <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . auth()->user()->name }}">
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">Sign Out</button>
            </form>
        </div>
    </nav>

    <div class="page-wrapper">
        @if(session('success') || session('error'))
        <div class="flash-zone">
            @if(session('success'))<div class="flash flash-success">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="flash flash-error">{{ session('error') }}</div>@endif
        </div>
        @endif
        <div class="page-content">
            @yield('content')
        </div>
    </div>

    @stack('scripts')
</body>
</html>


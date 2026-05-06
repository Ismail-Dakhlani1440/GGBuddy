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
            --bg:           #06070d;
            --surface:      rgba(255, 255, 255, 0.03);
            --surface-solid:#11121d;
            --surface2:     rgba(255, 255, 255, 0.05);
            --surface3:     rgba(255, 255, 255, 0.08);
            --border:       rgba(255, 255, 255, 0.08);
            --border-glow:  rgba(124, 58, 237, 0.3);
            --accent:       #8b5cf6;
            --accent-glow:  rgba(139, 92, 246, 0.5);
            --accent-light: #a78bfa;
            --text:         #f8fafc;
            --text-2:       #94a3b8;
            --text-3:       #475569;
            --red:          #f43f5e;
            --green:        #10b981;
            --glass:        rgba(15, 17, 26, 0.7);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; line-height: 1.5; }

        .top-nav {
            position: fixed; top: 0; left: 0; right: 0;
            background: var(--glass); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border); z-index: 1000;
            display: flex; align-items: center; height: 72px; padding: 0 40px;
        }

        .logo {
            font-size: 20px; font-weight: 900; letter-spacing: -0.05em;
            background: linear-gradient(135deg, #fff 0%, var(--accent-light) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            text-decoration: none; margin-right: 48px;
        }

        .nav-links { display: flex; align-items: center; gap: 8px; }
        .nav-link {
            padding: 10px 20px; border-radius: 12px; font-size: 14px; font-weight: 600;
            color: var(--text-2); text-decoration: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-link:hover { color: white; background: var(--surface2); }
        .nav-link.active { color: white; background: rgba(139, 92, 246, 0.15); border: 1px solid rgba(139, 92, 246, 0.2); }

        .nav-right { margin-left: auto; }

        .page-wrapper { padding-top: 72px; min-height: 100vh; }
        .page-content { max-width: 1200px; margin: 0 auto; padding: 48px 40px; animation: fadeIn 0.4s ease-out; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .btn {
            padding: 12px 24px; border-radius: 14px; font-size: 14px; font-weight: 800;
            text-decoration: none; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            font-family: 'Inter', sans-serif;
        }
        .btn-ghost { background: var(--surface); color: var(--text-2); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--surface2); color: white; border-color: var(--text-2); }
        
        .btn-primary { 
            background: linear-gradient(135deg, var(--accent) 0%, #6d28d9 100%); 
            color: white; 
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4); 
            border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(124, 58, 237, 0.5); }

        .form-group { display: flex; flex-direction: column; gap: 10px; }
        .form-label { font-size: 12px; font-weight: 800; color: var(--text-2); text-transform: uppercase; letter-spacing: 0.1em; }
        .form-input {
            background: rgba(0,0,0,0.2); border: 1px solid var(--border); border-radius: 14px;
            padding: 14px 18px; color: white; font-family: 'Inter', sans-serif; font-size: 15px;
            transition: all 0.2s;
        }
        .form-input:focus { outline: none; border-color: var(--accent); background: rgba(139, 92, 246, 0.05); box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1); }
        
        .card { 
            background: var(--surface-solid); 
            border: 1px solid var(--border); 
            border-radius: 28px; 
            padding: 40px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        }
        
        .flash-zone { max-width: 1200px; margin: 0 auto; padding: 24px 40px 0; }
        .flash { padding: 16px 20px; border-radius: 16px; font-size: 14px; font-weight: 600; border: 1px solid transparent; display: flex; align-items: center; gap: 12px; }
        .flash-success { background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); color: #34d399; }
        .flash-error { background: rgba(244, 63, 94, 0.1); border-color: rgba(244, 63, 94, 0.2); color: #fb7185; }
    </style>
</head>
<body>
    <nav class="top-nav">
        <a href="{{ route('admin.dashboard') }}" class="logo">GGBuddy Admin</a>
        <div class="nav-links">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Overview</a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a>
            <a href="{{ route('admin.games.index') }}" class="nav-link {{ request()->routeIs('admin.games.*') ? 'active' : '' }}">Games</a>
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

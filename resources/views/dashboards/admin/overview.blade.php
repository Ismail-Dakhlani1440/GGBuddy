@extends('layouts.admin', ['title' => 'Admin Overview'])

@section('content')
<div style="max-width:1100px;">
    
    <div style="margin-bottom:32px;">
        <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">Admin Dashboard</h1>
        <p style="font-size:14px;color:var(--text-2);margin-top:4px;">Command center for platform oversight and community safety.</p>
    </div>

    <!-- Stats Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:20px;margin-bottom:40px;">
        
        <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:24px;padding:24px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:40px;height:40px;background:rgba(124,58,237,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-user-clock" style="color:var(--accent);font-size:18px;"></i>
                </div>
                <span style="font-size:13px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:0.05em;">Pending Buddies</span>
            </div>
            <div style="font-size:32px;font-weight:900;">{{ $pendingEbuddiesCount }}</div>
            <a href="{{ route('admin.ebuddies.index') }}" style="display:block;margin-top:16px;font-size:12px;color:var(--accent);text-decoration:none;font-weight:700;">Review Applications →</a>
        </div>

        <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:24px;padding:24px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:40px;height:40px;background:rgba(239,68,68,0.1);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-shield-halved" style="color:var(--red);font-size:18px;"></i>
                </div>
                <span style="font-size:13px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:0.05em;">Open Reports</span>
            </div>
            <div style="font-size:32px;font-weight:900;">{{ $reportsCount }}</div>
            <a href="{{ route('admin.reports.index') }}" style="display:block;margin-top:16px;font-size:12px;color:var(--red);text-decoration:none;font-weight:700;">View Incidents →</a>
        </div>

        <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:24px;padding:24px;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.05);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-users" style="color:white;font-size:18px;"></i>
                </div>
                <span style="font-size:13px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:0.05em;">Total Users</span>
            </div>
            <div style="font-size:32px;font-weight:900;">{{ $usersCount }}</div>
            <p style="margin-top:16px;font-size:12px;color:var(--text-3);font-weight:600;">Registered accounts</p>
        </div>

    </div>

    <!-- Quick Actions -->
    <div style="background:linear-gradient(135deg, rgba(124,58,237,0.1) 0%, rgba(0,0,0,0) 100%);border:1px solid var(--border);border-radius:32px;padding:40px;display:flex;align-items:center;justify-content:space-between;gap:40px;flex-wrap:wrap;">
        <div style="max-width:500px;">
            <h2 style="font-size:1.5rem;font-weight:900;margin-bottom:12px;">Quick Maintenance</h2>
            <p style="color:var(--text-2);font-size:15px;line-height:1.6;">Maintain platform integrity by reviewing pending applications and community reports. Your oversight ensures a premium experience for all users.</p>
        </div>
        <div style="display:flex;gap:16px;">
            <a href="{{ route('admin.ebuddies.index') }}" class="btn btn-primary" style="padding:14px 32px;font-size:14px;">Review Applications</a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost" style="padding:14px 32px;font-size:14px;">User Reports</a>
        </div>
    </div>

</div>
@endsection

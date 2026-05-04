@extends('layouts.dashboard', ['title' => 'Overview'])

@section('content')
<div style="display:flex;flex-direction:column;gap:24px;">

    <div>
        <h1 style="font-size:1.6rem;font-weight:800;letter-spacing:-0.02em;">Welcome back, {{ auth()->user()->display_name ?? auth()->user()->name }}! 🎮</h1>
        <p style="font-size:14px;color:var(--text-2);margin-top:4px;">Here is what's happening with your E-Buddy account today.</p>
    </div>

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;">
        
        <!-- Total Orders -->
        <div class="card" style="padding:24px;display:flex;flex-direction:column;gap:12px;background:linear-gradient(135deg, var(--surface), rgba(124,58,237,0.03));">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:13px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:0.05em;">Total Sessions</span>
                <span style="padding:6px;background:rgba(124,58,237,0.1);border-radius:8px;color:var(--accent-light);">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20v-6M6 20V10M18 20V4"></path></svg>
                </span>
            </div>
            <span style="font-size:2rem;font-weight:800;color:var(--text);">{{ $totalOrders }}</span>
        </div>

        <!-- Pending Orders -->
        <div class="card" style="padding:24px;display:flex;flex-direction:column;gap:12px;background:linear-gradient(135deg, var(--surface), rgba(245,158,11,0.03));">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:13px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:0.05em;">Action Required</span>
                <span style="padding:6px;background:rgba(245,158,11,0.1);border-radius:8px;color:var(--yellow);">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
                </span>
            </div>
            <span style="font-size:2rem;font-weight:800;color:var(--text);">{{ $pendingOrders }}</span>
            @if($pendingOrders > 0)
                <a href="{{ route('ebuddy.orders') }}" style="font-size:12px;font-weight:600;color:var(--yellow);text-decoration:none;">Review now →</a>
            @endif
        </div>

        <!-- Earnings -->
        <div class="card" style="padding:24px;display:flex;flex-direction:column;gap:12px;background:linear-gradient(135deg, var(--surface), rgba(34,197,94,0.03));">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:13px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:0.05em;">Total Earnings</span>
                <span style="padding:6px;background:rgba(34,197,94,0.1);border-radius:8px;color:var(--green);">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </span>
            </div>
            <span style="font-size:2rem;font-weight:800;color:var(--text);">${{ number_format($totalEarnings, 2) }}</span>
        </div>

        <!-- Rating -->
        <div class="card" style="padding:24px;display:flex;flex-direction:column;gap:12px;background:linear-gradient(135deg, var(--surface), rgba(236,72,153,0.03));">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:13px;font-weight:600;color:var(--text-2);text-transform:uppercase;letter-spacing:0.05em;">Global Rating</span>
                <span style="padding:6px;background:rgba(236,72,153,0.1);border-radius:8px;color:#ec4899;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </span>
            </div>
            <span style="font-size:2rem;font-weight:800;color:var(--text);">{{ number_format($ebuddy->global_rating, 1) }}<span style="font-size:1rem;color:var(--text-2);">/5.0</span></span>
        </div>

    </div>

    {{-- Quick Actions --}}
    <div class="card" style="padding:28px;background:var(--surface2);">
        <p class="section-title" style="margin-bottom:16px;">Quick Actions</p>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <a href="{{ route('ebuddy.services') }}" class="btn btn-primary">Manage Services</a>
            <a href="{{ route('ebuddy.schedule') }}" class="btn btn-ghost">Update Availability</a>
            <a href="{{ route('ebuddy.profile') }}" class="btn btn-ghost">View Profile</a>
        </div>
    </div>

</div>
@endsection

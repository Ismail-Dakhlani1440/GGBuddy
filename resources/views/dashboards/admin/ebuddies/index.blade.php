@extends('layouts.admin', ['title' => 'E-Buddy Applications'])

@section('content')
<div style="max-width:1100px;">
    
    <div style="margin-bottom:32px;">
        <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">E-Buddy Applications</h1>
        <p style="font-size:14px;color:var(--text-2);margin-top:4px;">Verify and approve new E-Buddies joining the platform.</p>
    </div>

    @if($applications->isEmpty())
        <div style="background:rgba(255,255,255,0.03);border:1px dashed var(--border);border-radius:24px;padding:60px 20px;text-align:center;">
            <div style="width:64px;height:64px;background:rgba(255,255,255,0.05);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <i class="fa-solid fa-user-clock" style="font-size:24px;color:var(--text-3);"></i>
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:8px;">No pending applications</h3>
            <p style="color:var(--text-3);font-size:14px;">You're all caught up! All registrations have been processed.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(340px, 1fr));gap:24px;">
            @foreach($applications as $ebuddy)
            <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:24px;overflow:hidden;display:flex;flex-direction:column;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-4px)';this.style.borderColor='rgba(255,255,255,0.1)'" onmouseout="this.style.transform='none';this.style.borderColor='var(--border)'">
                <div style="padding:24px;">
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
                        <div style="width:56px;height:56px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:20px;color:white;flex-shrink:0;">
                            {{ substr($ebuddy->user->name, 0, 1) }}
                        </div>
                        <div style="overflow:hidden;">
                            <h3 style="font-size:1.1rem;font-weight:800;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ebuddy->user->name }}</h3>
                            <p style="font-size:13px;color:var(--text-3);margin:4px 0 0;">{{ $ebuddy->user->email }}</p>
                        </div>
                    </div>

                    <div style="margin-bottom:24px;">
                        <label style="display:block;font-size:11px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px;font-weight:800;">Bio</label>
                        <p style="font-size:14px;color:var(--text-2);line-height:1.5;margin:0;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $ebuddy->bio ?: 'No bio provided.' }}
                        </p>
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;">
                        <span style="font-size:12px;color:var(--text-3);">Applied {{ $ebuddy->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div style="padding:20px;background:rgba(255,255,255,0.02);border-top:1px solid var(--border);display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <form action="{{ route('admin.ebuddies.reject', $ebuddy) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;padding:10px;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:12px;color:var(--error);cursor:pointer;font-size:13px;font-weight:800;transition:all 0.2s;" onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                            Reject
                        </button>
                    </form>
                    <form action="{{ route('admin.ebuddies.approve', $ebuddy) }}" method="POST">
                        @csrf
                        <button type="submit" style="width:100%;padding:10px;background:var(--accent);border:none;border-radius:12px;color:white;cursor:pointer;font-size:13px;font-weight:800;transition:all 0.2s;" onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'">
                            Approve
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:32px;">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection

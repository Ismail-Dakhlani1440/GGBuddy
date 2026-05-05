@extends('layouts.admin', ['title' => 'Report Details'])

@section('content')
<div style="max-width:800px;">
    
    <div style="margin-bottom:32px;">
        <a href="{{ route('admin.reports.index') }}" style="color:var(--text-3);text-decoration:none;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <i class="fa-solid fa-arrow-left"></i> Back to Reports
        </a>
        <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">Report Details</h1>
    </div>

    <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:24px;padding:32px;margin-bottom:24px;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px;margin-bottom:40px;">
            <div>
                <label style="display:block;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;font-weight:700;">Reporter</label>
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;color:white;">
                        {{ substr($report->reporter->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;">{{ $report->reporter->name }}</div>
                        <div style="font-size:13px;color:var(--text-3);">{{ $report->reporter->email }}</div>
                    </div>
                </div>
            </div>
            <div>
                <label style="display:block;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;font-weight:700;">Reported User (Target)</label>
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;color:white;">
                        {{ substr($report->target->name, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:15px;color:{{ $report->target->is_suspended ? 'var(--error)' : 'white' }}">
                            {{ $report->target->name }}
                        </div>
                        <div style="font-size:13px;color:var(--text-3);">{{ $report->target->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-bottom:40px;">
            <label style="display:block;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;font-weight:700;">Report Reason</label>
            <div style="background:rgba(255,255,255,0.03);border:1px solid var(--border);border-radius:16px;padding:24px;font-size:15px;line-height:1.6;color:var(--text-2);">
                {{ $report->reason }}
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:32px;border-top:1px solid var(--border);">
            <div style="color:var(--text-3);font-size:14px;">
                Submitted {{ $report->created_at->format('F j, Y \a\t g:i a') }}
            </div>
            <div style="display:flex;gap:12px;">
                <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            style="padding:12px 24px;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:12px;color:var(--text-2);cursor:pointer;font-size:14px;font-weight:700;transition:all 0.2s;"
                            onmouseover="this.style.color='white';this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.color='var(--text-2)';this.style.background='rgba(255,255,255,0.05)'">
                        Dismiss Report
                    </button>
                </form>
                <form action="{{ route('admin.users.suspend', $report->target) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            style="padding:12px 24px;background:{{ $report->target->is_suspended ? 'var(--success)' : 'var(--error)' }};border:none;border-radius:12px;color:white;cursor:pointer;font-size:14px;font-weight:800;transition:all 0.2s;"
                            onmouseover="this.style.filter='brightness(1.1)'" onmouseout="this.style.filter='none'">
                        {{ $report->target->is_suspended ? 'Unsuspend Account' : 'Suspend Account' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin', ['title' => 'User Reports'])

@section('content')
<div style="max-width:1100px;">
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">User Reports</h1>
            <p style="font-size:14px;color:var(--text-2);margin-top:4px;">Review and action safety reports from the community.</p>
        </div>
    </div>

    @if($reports->isEmpty())
        <div style="background:rgba(255,255,255,0.03);border:1px dashed var(--border);border-radius:24px;padding:60px 20px;text-align:center;">
            <div style="width:64px;height:64px;background:rgba(255,255,255,0.05);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <i class="fa-solid fa-shield-halved" style="font-size:24px;color:var(--text-3);"></i>
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:8px;">No reports found</h3>
            <p style="color:var(--text-3);font-size:14px;">Great! The community seems to be behaving well.</p>
        </div>
    @else
        <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:20px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;text-align:left;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);background:rgba(255,255,255,0.02);">
                        <th style="padding:16px 24px;font-size:13px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">Reporter</th>
                        <th style="padding:16px 24px;font-size:13px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">Target</th>
                        <th style="padding:16px 24px;font-size:13px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">Reason</th>
                        <th style="padding:16px 24px;font-size:13px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">Date</th>
                        <th style="padding:16px 24px;font-size:13px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.03);transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:16px 24px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:32px;height:32px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:12px;color:white;">
                                    {{ substr($report->reporter->name, 0, 1) }}
                                </div>
                                <span style="font-weight:600;font-size:14px;">{{ $report->reporter->name }}</span>
                            </div>
                        </td>
                        <td style="padding:16px 24px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <span style="font-weight:600;font-size:14px;color:{{ $report->target->is_suspended ? 'var(--error)' : 'white' }}">
                                    {{ $report->target->name }}
                                </span>
                                @if($report->target->is_suspended)
                                    <span style="background:rgba(239, 68, 68, 0.1);color:var(--error);font-size:10px;padding:2px 6px;border-radius:4px;font-weight:800;text-transform:uppercase;">Suspended</span>
                                @endif
                            </div>
                        </td>
                        <td style="padding:16px 24px;">
                            <p style="font-size:14px;color:var(--text-2);max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin:0;">
                                {{ $report->reason }}
                            </p>
                        </td>
                        <td style="padding:16px 24px;font-size:13px;color:var(--text-3);">
                            {{ $report->created_at->diffForHumans() }}
                        </td>
                        <td style="padding:16px 24px;text-align:right;">
                            <div style="display:flex;justify-content:flex-end;gap:8px;">
                                <a href="{{ route('admin.reports.show', $report) }}" 
                                   style="padding:6px 12px;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:8px;color:white;text-decoration:none;font-size:12px;font-weight:700;transition:all 0.2s;"
                                   onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                                    View Details
                                </a>
                                <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" 
                                            style="padding:6px 12px;background:rgba(255,255,255,0.03);border:1px solid var(--border);border-radius:8px;color:var(--text-3);cursor:pointer;font-size:12px;font-weight:700;transition:all 0.2s;"
                                            onmouseover="this.style.color='white';this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.color='var(--text-3)';this.style.background='rgba(255,255,255,0.03)'">
                                        Dismiss
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.suspend', $report->target) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" 
                                            style="padding:6px 12px;background:{{ $report->target->is_suspended ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)' }};border:1px solid {{ $report->target->is_suspended ? 'var(--success)' : 'var(--error)' }};border-radius:8px;color:{{ $report->target->is_suspended ? 'var(--success)' : 'var(--error)' }};cursor:pointer;font-size:12px;font-weight:700;transition:all 0.2s;"
                                            onclick="return confirm('Are you sure you want to {{ $report->target->is_suspended ? 'unsuspend' : 'suspend' }} this user?')">
                                        {{ $report->target->is_suspended ? 'Unsuspend' : 'Suspend' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:24px;">
            {{ $reports->links() }}
        </div>
    @endif
</div>
@endsection

@extends('layouts.admin', ['title' => 'User Management'])

@section('content')
<div style="max-width:1100px;">
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">User Management</h1>
            <p style="font-size:14px;color:var(--text-2);margin-top:4px;">Manage all registered accounts and enforce community guidelines.</p>
        </div>
    </div>

    @if($users->isEmpty())
        <div style="background:rgba(255,255,255,0.02);border:1px dashed var(--border);border-radius:24px;padding:60px;text-align:center;">
            <p style="color:var(--text-3);">No users found.</p>
        </div>
    @else
        <div style="background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:20px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;text-align:left;">
                <thead>
                    <tr style="border-bottom:1px solid var(--border);background:rgba(255,255,255,0.02);">
                        <th style="padding:16px 24px;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">User</th>
                        <th style="padding:16px 24px;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">Role</th>
                        <th style="padding:16px 24px;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="padding:16px 24px;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;">Joined</th>
                        <th style="padding:16px 24px;font-size:12px;color:var(--text-3);text-transform:uppercase;letter-spacing:0.05em;text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.03);transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.01)'" onmouseout="this.style.background='transparent'">
                        <td style="padding:16px 24px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:36px;height:36px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:14px;color:white;flex-shrink:0;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-weight:700;font-size:14px;">{{ $user->name }}</div>
                                    <div style="font-size:12px;color:var(--text-3);">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:16px 24px;">
                            <span style="font-size:12px;font-weight:700;text-transform:uppercase;color:{{ $user->isEBuddy() ? 'var(--accent)' : ($user->isAdmin() ? 'var(--red)' : 'var(--text-2)') }}">
                                {{ $user->role->title }}
                            </span>
                        </td>
                        <td style="padding:16px 24px;">
                            @if($user->is_suspended)
                                <span style="background:rgba(239, 68, 68, 0.1);color:var(--red);font-size:10px;padding:4px 8px;border-radius:6px;font-weight:800;text-transform:uppercase;border:1px solid rgba(239, 68, 68, 0.2);">Suspended</span>
                            @else
                                <span style="background:rgba(34, 197, 94, 0.1);color:var(--green);font-size:10px;padding:4px 8px;border-radius:6px;font-weight:800;text-transform:uppercase;border:1px solid rgba(34, 197, 94, 0.2);">Active</span>
                            @endif
                        </td>
                        <td style="padding:16px 24px;font-size:13px;color:var(--text-3);">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td style="padding:16px 24px;text-align:right;">
                            <form action="{{ route('admin.users.suspend', $user) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        style="padding:8px 16px;background:{{ $user->is_suspended ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)' }};border:1px solid {{ $user->is_suspended ? 'var(--green)' : 'var(--red)' }};border-radius:10px;color:{{ $user->is_suspended ? 'var(--green)' : 'var(--red)' }};cursor:pointer;font-size:12px;font-weight:800;transition:all 0.2s;"
                                        onclick="return confirm('Are you sure you want to {{ $user->is_suspended ? 'unsuspend' : 'suspend' }} this user?')">
                                    {{ $user->is_suspended ? 'Unsuspend' : 'Suspend User' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:24px;">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

@extends('layouts.admin', ['title' => 'Game Management'])

@section('content')
<div style="max-width:1100px;">
    
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;">
        <div>
            <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">Game Library</h1>
            <p style="font-size:14px;color:var(--text-2);margin-top:4px;">Add and manage games available for services on the platform.</p>
        </div>
        <a href="{{ route('admin.games.create') }}" class="btn btn-primary" style="padding:10px 20px;font-size:13px;font-weight:800;border-radius:12px;">
            Add New Game
        </a>
    </div>

    @if($games->isEmpty())
        <div style="background:rgba(255,255,255,0.02);border:1px dashed var(--border);border-radius:24px;padding:60px;text-align:center;">
            <p style="color:var(--text-3);">No games added yet.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:24px;">
            @foreach($games as $game)
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:20px;overflow:hidden;display:flex;flex-direction:column;transition:all 0.3s;" onmouseover="this.style.borderColor='var(--accent)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)'">
                <div style="height:160px;background:#111;overflow:hidden;position:relative;">
                    @if($game->cover)
                        <img src="{{ asset('storage/'.$game->cover) }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-3);font-size:12px;font-weight:700;text-transform:uppercase;">No Cover</div>
                    @endif
                </div>
                <div style="padding:20px;flex:1;display:flex;flex-direction:column;">
                    <h3 style="font-size:1.1rem;font-weight:900;margin-bottom:8px;">{{ $game->title }}</h3>
                    <p style="font-size:13px;color:var(--text-3);line-height:1.5;margin-bottom:20px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $game->description ?? 'No description provided.' }}
                    </p>
                    
                    <div style="margin-top:auto;display:flex;align-items:center;justify-content:space-between;padding-top:16px;border-top:1px solid rgba(255,255,255,0.03);">
                        <span style="font-size:12px;font-weight:800;color:var(--accent-light);">{{ $game->ranks_count }} Ranks</span>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('admin.games.edit', $game) }}" style="padding:6px;color:var(--text-2);background:rgba(255,255,255,0.03);border:1px solid var(--border);border-radius:8px;transition:0.2s;" onmouseover="this.style.color='white';this.style.background='var(--accent)'" onmouseout="this.style.color='var(--text-2)';this.style.background='rgba(255,255,255,0.03)'">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.games.destroy', $game) }}" method="POST" onsubmit="return confirm('Archive this game?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding:6px;color:var(--red);background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1);border-radius:8px;cursor:pointer;transition:0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='rgba(239,68,68,0.05)'">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div style="margin-top:32px;">
            {{ $games->links() }}
        </div>
    @endif
</div>
@endsection

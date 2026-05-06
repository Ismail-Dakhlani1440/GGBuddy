@extends('layouts.admin', ['title' => 'Game Library'])

@section('content')
<div style="max-width:1200px; margin: 0 auto;">
    
    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:48px; animation: fadeInDown 0.6s ease-out;">
        <div>
            <h1 style="font-size:2.8rem; font-weight:900; letter-spacing:-0.05em; background:linear-gradient(135deg, #fff 0%, var(--accent-light) 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Game Library</h1>
            <p style="font-size:16px; color:var(--text-2); margin-top:8px;">Command center for managing platform games and rank ecosystems.</p>
        </div>
        <a href="{{ route('admin.games.create') }}" class="btn btn-primary" style="padding:16px 32px; font-size:15px; border-radius:20px; box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:10px;"><path d="M12 4v16m8-8H4"></path></svg>
            Add New Game
        </a>
    </div>

    {{-- Grid --}}
    @if($games->isEmpty())
        <div style="background:var(--surface); border:2px dashed var(--border); border-radius:40px; padding:100px; text-align:center; animation: fadeIn 0.8s ease-out;">
            <div style="width:80px; height:80px; background:rgba(139, 92, 246, 0.1); border-radius:24px; display:flex; align-items:center; justify-content:center; margin:0 auto 32px; color:var(--accent);">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path></svg>
            </div>
            <h3 style="font-weight:900; font-size:1.5rem; margin-bottom:12px;">No Games Found</h3>
            <p style="color:var(--text-3); max-width:350px; margin:0 auto; line-height:1.6;">Your game catalog is empty. Start by adding a game to enable e-buddy services.</p>
        </div>
    @else
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(350px, 1fr)); gap:32px;">
            @foreach($games as $game)
            <div style="background:var(--surface-solid); border:1px solid var(--border); border-radius:36px; overflow:hidden; display:flex; flex-direction:column; transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1); position:relative;" 
                 onmouseover="this.style.borderColor='var(--accent)'; this.style.transform='translateY(-10px)'; this.style.boxShadow='0 25px 50px rgba(0,0,0,0.5)'" 
                 onmouseout="this.style.borderColor='var(--border)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                
                <div style="height:220px; background:#000; overflow:hidden; position:relative;">
                    <img src="{{ $game->cover ? Storage::disk('public')->url($game->cover) : 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=800' }}" 
                         style="width:100%; height:100%; object-fit:cover; transition:0.6s ease;" 
                         onmouseover="this.style.transform='scale(1.1) rotate(1deg)'" 
                         onmouseout="this.style.transform='scale(1)'">
                    
                    <div style="position:absolute; top:20px; left:20px; background:rgba(15, 17, 26, 0.6); backdrop-filter:blur(12px); padding:8px 16px; border-radius:14px; font-size:11px; font-weight:900; color:var(--text); border:1px solid rgba(255,255,255,0.1); text-transform:uppercase; letter-spacing:0.05em;">
                        {{ $game->ranks_count }} Ranks Defined
                    </div>
                </div>

                {{-- Info --}}
                <div style="padding:32px; flex:1; display:flex; flex-direction:column;">
                    <h3 style="font-size:1.6rem; font-weight:900; letter-spacing:-0.03em; margin-bottom:12px; color:#fff;">{{ $game->title }}</h3>
                    <p style="font-size:14px; color:var(--text-2); line-height:1.6; margin-bottom:32px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; opacity:0.8;">
                        {{ $game->description ?? 'No strategic overview provided for this title yet.' }}
                    </p>
                    
                    <div style="margin-top:auto; display:flex; gap:12px; padding-top:24px; border-top:1px solid var(--border);">
                        <a href="{{ route('admin.games.edit', $game) }}" class="btn btn-ghost" style="flex:1; border-radius:16px; padding:12px; font-weight:800; background:var(--surface3);">
                            Configure
                        </a>
                        <form action="{{ route('admin.games.destroy', $game) }}" method="POST" onsubmit="return confirm('Archive this game? This will hide it from the platform.')" style="display:contents;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost" style="aspect-ratio:1; padding:0; width:48px; border-radius:16px; color:var(--red); background:rgba(244, 63, 94, 0.05); border-color:transparent;">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div style="margin-top:64px; display:flex; justify-content:center;">
            {{ $games->links() }}
        </div>
    @endif
</div>

<style>
    @keyframes fadeInDown { from { opacity:0; transform:translateY(-20px); } to { opacity:1; transform:translateY(0); } }
</style>
@endsection

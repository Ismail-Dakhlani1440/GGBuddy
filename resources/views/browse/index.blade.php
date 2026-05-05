@extends($layout, ['title' => 'Find an E-Buddy'])

@section('content')
<div style="display:flex;flex-direction:column;gap:24px;">

    {{-- Header --}}
    <div>
        <h1 style="font-size:1.6rem;font-weight:800;letter-spacing:-0.03em;">Find an E-Buddy</h1>
        <p style="font-size:13px;color:var(--text-2);margin-top:5px;">Browse available E-Buddies and book a session.</p>
    </div>

    {{-- E-Buddy Grid --}}
    @if($ebuddies->isEmpty())
        <div style="padding:60px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;">
            <p style="color:var(--text-2);font-size:14px;">No E-Buddies available right now. Check back soon!</p>
        </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;">
        @foreach($ebuddies as $ebuddy)
        <a href="{{ route('browse.show', $ebuddy) }}" style="text-decoration:none;display:block;">
            <div class="card" style="overflow:hidden;display:flex;flex-direction:column;height:100%;transition:transform 0.2s,border-color 0.2s;" onmouseover="this.style.transform='translateY(-3px)';this.style.borderColor='rgba(124,58,237,0.35)'" onmouseout="this.style.transform='translateY(0)';this.style.borderColor='var(--border)'">
                {{-- Banner --}}
                <div style="height:80px;background:linear-gradient(135deg,#1a1040,#130b2e);position:relative;overflow:hidden;">
                    @if($ebuddy->eBuddy && $ebuddy->eBuddy->banner)
                        <img src="{{ asset('storage/'.$ebuddy->eBuddy->banner) }}" style="width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;">
                    @else
                        <div style="position:absolute;top:-40px;left:-20px;width:200px;height:200px;background:radial-gradient(circle,rgba(124,58,237,0.2) 0%,transparent 65%);"></div>
                    @endif
                </div>
                {{-- Avatar --}}
                <div style="padding:0 20px;margin-top:-28px;margin-bottom:14px;position:relative;z-index:10;">
                    <div style="width:56px;height:56px;border-radius:50%;overflow:hidden;border:3px solid var(--bg);">
                        <img src="{{ $ebuddy->avatar ? asset('storage/'.$ebuddy->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$ebuddy->name }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                </div>
                {{-- Info --}}
                <div style="padding:0 20px 20px;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <h3 style="font-size:15px;font-weight:700;color:var(--text);">{{ $ebuddy->display_name ?? $ebuddy->name }}</h3>
                        <div style="display:flex;align-items:center;gap:3px;background:rgba(250,204,21,0.1);color:#facc15;padding:2px 6px;border-radius:6px;font-size:10px;font-weight:700;">
                            <svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ number_format($ebuddy->eBuddy->global_rating ?? 0, 1) }}
                        </div>
                    </div>
                    <p style="font-size:12px;color:var(--text-2);margin-bottom:12px;line-height:1.5;">
                        {{ Str::limit($ebuddy->eBuddy?->bio ?? 'Available for gaming sessions.', 80) }}
                    </p>
                    {{-- Games --}}
                    @if($ebuddy->gameProfiles->count())
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;">
                        @foreach($ebuddy->gameProfiles->take(3) as $gp)
                        <span style="padding:2px 10px;border-radius:100px;font-size:10px;font-weight:600;background:var(--surface2);color:var(--text-2);border:1px solid var(--border);">{{ $gp->game->title }}</span>
                        @endforeach
                        @if($ebuddy->gameProfiles->count() > 3)
                        <span style="padding:2px 10px;border-radius:100px;font-size:10px;font-weight:600;color:var(--text-3);">+{{ $ebuddy->gameProfiles->count() - 3 }} more</span>
                        @endif
                    </div>
                    @endif
                    {{-- Price from --}}
                    @if($ebuddy->eBuddy && $ebuddy->eBuddy->services->count())
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:11px;color:var(--text-2);">From</span>
                        <span style="font-size:15px;font-weight:800;color:var(--text);">${{ number_format($ebuddy->eBuddy->services->min('price'), 0) }}<span style="font-size:11px;font-weight:500;color:var(--text-2);">/hr</span></span>
                    </div>
                    @else
                    <p style="font-size:12px;color:var(--text-3);">No services listed yet</p>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection

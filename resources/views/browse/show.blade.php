@extends($layout, ['title' => $ebuddy->display_name ?? $ebuddy->name])

@section('content')

{{-- Profile Banner --}}
<div style="position:relative;margin:-32px -24px 0;background:linear-gradient(135deg,#1a1040 0%,#0b0c16 40%,#130b2e 100%);overflow:hidden;">
    @if($ebuddy->eBuddy && $ebuddy->eBuddy->banner)
        <img src="{{ asset('storage/'.$ebuddy->eBuddy->banner) }}" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;pointer-events:none;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(to top, var(--bg) 0%, transparent 100%);pointer-events:none;"></div>
    @else
        <div style="position:absolute;top:-60px;left:10%;width:500px;height:500px;background:radial-gradient(circle,rgba(124,58,237,0.25) 0%,transparent 65%);pointer-events:none;"></div>
        <div style="position:absolute;top:-40px;right:5%;width:400px;height:400px;background:radial-gradient(circle,rgba(157,95,245,0.12) 0%,transparent 65%);pointer-events:none;"></div>
    @endif

    <div style="position:relative;padding:40px 24px 0;">
        <div style="display:flex;align-items:flex-end;gap:20px;flex-wrap:wrap;">

            {{-- Avatar --}}
            <div style="flex-shrink:0;position:relative;margin-bottom:-28px;">
                <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:4px solid var(--bg);">
                    <img src="{{ $ebuddy->avatar ? asset('storage/'.$ebuddy->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$ebuddy->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>

            </div>

            {{-- Name --}}
            <div style="padding-bottom:28px;min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 style="font-size:1.5rem;font-weight:800;letter-spacing:-0.03em;">{{ $ebuddy->display_name ?? $ebuddy->name }}</h1>
                    <span class="badge badge-purple">Pro E-Buddy</span>
                </div>
                <p style="font-size:13px;color:var(--text-2);">Member since {{ $ebuddy->created_at->format('M Y') }}</p>
            </div>

            {{-- Back --}}
            <div style="padding-bottom:28px;flex-shrink:0;">
                <a href="{{ route('browse.index') }}" class="btn btn-ghost btn-sm">← Back</a>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;margin-top:28px;border-bottom:1px solid var(--border);">
            @php $activeTab = request('tab', 'about'); @endphp
            @foreach(['about' => 'About', 'services' => 'Services (' . ($ebuddy->eBuddy ? $ebuddy->eBuddy->services->count() : 0) . ')'] as $key => $label)
            <a href="?tab={{ $key }}" style="padding:12px 20px;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:-1px;border-bottom:2px solid {{ $activeTab === $key ? 'var(--accent-light)' : 'transparent' }};color:{{ $activeTab === $key ? 'var(--text)' : 'var(--text-2)' }};transition:color 0.15s;">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Tab Content --}}
<div style="padding-top:32px;">

    @if($activeTab === 'about')
    <div class="side-col">
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:10px;">About</p>
                <p style="font-size:14px;line-height:1.75;color:var(--text-2);">{{ $ebuddy->eBuddy?->bio ?? 'No bio provided.' }}</p>
            </div>
            @if($ebuddy->gameProfiles->count())
            <div class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:16px;">Games</p>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;">
                    @foreach($ebuddy->gameProfiles as $gp)
                    <div style="padding:14px 16px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);">
                        <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--accent-light);margin-bottom:4px;">{{ $gp->game->title }}</p>
                        <p style="font-size:14px;font-weight:700;color:var(--text);">{{ $gp->currentRank->title ?? 'Unranked' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="card" style="padding:20px;">
            <p class="section-title" style="margin-bottom:16px;">Stats</p>
            @php $stats = [
                ['label'=>'Rating',   'value'=> number_format($ebuddy->eBuddy?->global_rating ?? 0, 1).' ★', 'color'=>'var(--yellow)'],
                ['label'=>'Games',    'value'=> $ebuddy->gameProfiles->count(),                               'color'=>'var(--accent-light)'],
                ['label'=>'Services', 'value'=> $ebuddy->eBuddy ? $ebuddy->eBuddy->services->count() : 0,                                   'color'=>'var(--text)'],
            ]; @endphp
            @foreach($stats as $s)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);">
                <span style="font-size:13px;color:var(--text-2);">{{ $s['label'] }}</span>
                <span style="font-size:14px;font-weight:700;color:{{ $s['color'] }};">{{ $s['value'] }}</span>
            </div>
            @endforeach
            <a href="?tab=services" class="btn btn-primary" style="width:100%;margin-top:16px;justify-content:center;">Book a Session</a>
        </div>
    </div>

    @elseif($activeTab === 'services' && $ebuddy->eBuddy)
    <div style="display:flex;flex-direction:column;gap:14px;">
        @forelse($ebuddy->eBuddy->services as $service)
        <div class="card" style="padding:24px;" x-data="{ open: false }">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--accent-light);margin-bottom:5px;">{{ $service->game->title }}</p>
                    <h3 style="font-size:15px;font-weight:700;margin-bottom:8px;">{{ $service->title }}</h3>
                    <p style="font-size:13px;color:var(--text-2);line-height:1.6;margin-bottom:8px;">{{ $service->description }}</p>
                    <p style="font-size:12px;color:var(--text-2);">Rank: <strong style="color:var(--text);">{{ $service->rank->title }}</strong></p>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;flex-shrink:0;">
                    <p style="font-size:1.4rem;font-weight:800;color:var(--text);">${{ number_format($service->price, 0) }}<span style="font-size:12px;font-weight:500;color:var(--text-2);">/hr</span></p>
                    @if(auth()->id() !== $ebuddy->id)
                        <button @click="open=true" class="btn btn-primary btn-sm">Book Now</button>
                    @else
                        <span style="font-size:12px;color:var(--text-3);padding:8px 0;">Your service</span>
                    @endif
                </div>
            </div>

            {{-- Booking Modal --}}
            @if(auth()->id() !== $ebuddy->id)
            <div x-show="open" x-cloak style="position:fixed;inset:0;z-index:300;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(0,0,0,0.75);backdrop-filter:blur(8px);" @click.away="open=false">
                <div class="card" style="padding:28px;width:100%;max-width:420px;position:relative;" @click.stop>
                    <h3 style="font-size:1.1rem;font-weight:800;margin-bottom:4px;">{{ $service->title }}</h3>
                    <p style="font-size:13px;color:var(--text-2);margin-bottom:22px;">with {{ $ebuddy->display_name ?? $ebuddy->name }} · ${{ number_format($service->price, 0) }}/hr</p>
                    <form action="{{ route('browse.order', $service) }}" method="POST" style="display:flex;flex-direction:column;gap:16px;">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Duration (hours)</label>
                            <input type="number" name="hours" class="form-input" min="1" max="24" value="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Message (optional)</label>
                            <textarea name="message" class="form-input" rows="3" placeholder="Tell them what you need help with..."></textarea>
                        </div>
                        <div style="display:flex;gap:10px;padding-top:4px;">
                            <button type="button" @click="open=false" class="btn btn-ghost" style="flex:1;">Cancel</button>
                            <button type="submit" class="btn btn-primary" style="flex:1;">Confirm Booking</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
        @empty
        <div style="padding:48px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;">
            <p style="color:var(--text-2);">No services listed yet.</p>
        </div>
        @endforelse
    </div>
    @endif

</div>
@endsection

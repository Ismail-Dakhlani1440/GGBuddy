@extends('layouts.dashboard', ['title' => 'Profile'])

@section('content')

{{-- Profile Banner — negative margins bleed to edge of page-content padding --}}
<div style="position:relative;margin:-32px -24px 0;background:linear-gradient(135deg,#1a1040 0%,#0b0c16 40%,#130b2e 100%);overflow:hidden;">
    <div style="position:absolute;top:-60px;left:10%;width:500px;height:500px;background:radial-gradient(circle,rgba(124,58,237,0.25) 0%,transparent 65%);pointer-events:none;"></div>
    <div style="position:absolute;top:-40px;right:5%;width:400px;height:400px;background:radial-gradient(circle,rgba(157,95,245,0.12) 0%,transparent 65%);pointer-events:none;"></div>

    <div style="position:relative;padding:40px 24px 0;">
        <div style="display:flex;align-items:flex-end;gap:20px;flex-wrap:wrap;">

            {{-- Avatar --}}
            <div style="flex-shrink:0;position:relative;margin-bottom:-28px;">
                <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:4px solid var(--bg);background:var(--surface2);">
                    <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <span style="position:absolute;bottom:6px;right:4px;width:14px;height:14px;border-radius:50%;background:var(--green);border:3px solid var(--bg);box-shadow:0 0 8px rgba(34,197,94,0.6);"></span>
            </div>

            {{-- Name / meta --}}
            <div style="padding-bottom:28px;min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 style="font-size:1.5rem;font-weight:800;letter-spacing:-0.03em;">{{ $user->display_name ?? $user->name }}</h1>
                    <span class="badge badge-purple">Pro E-Buddy</span>
                </div>
                <p style="font-size:13px;color:var(--text-2);">Member since {{ $user->created_at->format('M Y') }} · {{ $user->timezone }}</p>
            </div>

            {{-- Action buttons --}}
            <div style="padding-bottom:28px;display:flex;gap:10px;flex-wrap:wrap;flex-shrink:0;">
                <a href="{{ route('ebuddy.profile.edit') }}" class="btn btn-primary btn-sm">Edit Profile</a>
                <a href="{{ route('ebuddy.profile.add-game') }}" class="btn btn-ghost btn-sm">Add Game</a>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;margin-top:28px;border-bottom:1px solid var(--border);">
            @php
                $tabs = [
                    ['id' => 'overview', 'label' => 'Overview'],
                    ['id' => 'games',    'label' => 'Games (' . $userProfiles->count() . ')'],
                ];
                $activeTab = request('tab', 'overview');
            @endphp
            @foreach($tabs as $tab)
                <a href="?tab={{ $tab['id'] }}" style="padding:12px 20px;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:-1px;border-bottom:2px solid {{ $activeTab === $tab['id'] ? 'var(--accent-light)' : 'transparent' }};color:{{ $activeTab === $tab['id'] ? 'var(--text)' : 'var(--text-2)' }};transition:color 0.15s;">
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Tab Content --}}
<div style="padding-top:32px;">

    @if($activeTab === 'overview')
    <div class="side-col">

        {{-- Main --}}
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:10px;">About</p>
                <p style="font-size:14px;line-height:1.75;color:var(--text-2);">
                    {{ $ebuddy->bio ?? 'No bio yet — edit your profile to add one.' }}
                </p>
            </div>

            @if($userProfiles->count())
            <div class="card" style="padding:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <p class="section-title">Games</p>
                    <a href="?tab=games" style="font-size:12px;font-weight:600;color:var(--accent-light);text-decoration:none;">See all →</a>
                </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;">
                    @foreach($userProfiles->take(4) as $profile)
                    <div style="padding:14px 16px;background:var(--surface2);border-radius:12px;border:1px solid var(--border);">
                        <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--accent-light);margin-bottom:4px;">{{ $profile->game->title }}</p>
                        <p style="font-size:14px;font-weight:700;color:var(--text);">{{ $profile->currentRank->title ?? 'Unranked' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar stats --}}
        <div class="card" style="padding:20px;">
            <p class="section-title" style="margin-bottom:16px;">Stats</p>
            @php $pstats = [
                ['label'=>'Rating',     'value'=> number_format($ebuddy->global_rating,1).' ★', 'color'=>'var(--yellow)'],
                ['label'=>'Completion', 'value'=>'98%',                                          'color'=>'var(--green)'],
                ['label'=>'Sessions',   'value'=>'42',                                           'color'=>'var(--accent-light)'],
                ['label'=>'Games',      'value'=> $userProfiles->count(),                        'color'=>'var(--text)'],
            ]; @endphp
            @foreach($pstats as $s)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);">
                <span style="font-size:13px;color:var(--text-2);">{{ $s['label'] }}</span>
                <span style="font-size:14px;font-weight:700;color:{{ $s['color'] }};">{{ $s['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    @elseif($activeTab === 'games')
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;justify-content:flex-end;">
            <a href="{{ route('ebuddy.profile.add-game') }}" class="btn btn-primary btn-sm">Add Game</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;">
            @forelse($userProfiles as $profile)
            <div class="card" style="padding:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--accent-light);margin-bottom:6px;">{{ $profile->game->title }}</p>
                    <p style="font-size:16px;font-weight:800;color:var(--text);">{{ $profile->currentRank->title ?? 'Unranked' }}</p>
                </div>
                <form action="{{ route('ebuddy.profile.remove-game', $profile) }}" method="POST" onsubmit="return confirm('Remove this game from your library?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                </form>
            </div>
            @empty
            <div style="grid-column:1/-1;padding:56px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;">
                <p style="color:var(--text-2);margin-bottom:18px;font-size:14px;">No games added yet.</p>
                <a href="{{ route('ebuddy.profile.add-game') }}" class="btn btn-primary">Add Your First Game</a>
            </div>
            @endforelse
        </div>
    </div>
    @endif

</div>
@endsection

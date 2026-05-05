@extends('layouts.dashboard', ['title' => 'Profile'])

@section('content')

{{-- Profile Banner — negative margins bleed to edge of page-content padding --}}
<div style="position:relative;margin:-32px -24px 0;background:linear-gradient(135deg,#1a1040 0%,#0b0c16 40%,#130b2e 100%);overflow:hidden;">
    @if($ebuddy && $ebuddy->banner)
        <img src="{{ asset('storage/'.$ebuddy->banner) }}" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;pointer-events:none;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(to top, var(--bg) 0%, transparent 100%);pointer-events:none;"></div>
    @else
        <div style="position:absolute;top:-60px;left:10%;width:500px;height:500px;background:radial-gradient(circle,rgba(124,58,237,0.25) 0%,transparent 65%);pointer-events:none;"></div>
        <div style="position:absolute;top:-40px;right:5%;width:400px;height:400px;background:radial-gradient(circle,rgba(157,95,245,0.12) 0%,transparent 65%);pointer-events:none;"></div>
    @endif

    <div style="position:relative;padding:40px 24px 0;">
        <div style="display:flex;align-items:flex-end;gap:20px;flex-wrap:wrap;">

            {{-- Avatar --}}
            <div style="flex-shrink:0;position:relative;margin-bottom:-28px;">
                <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:4px solid var(--bg);background:var(--surface2);">
                    <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>

            </div>

            {{-- Name / meta --}}
            <div style="padding-bottom:28px;min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 style="font-size:1.5rem;font-weight:800;letter-spacing:-0.03em;">{{ $user->display_name ?? $user->name }}</h1>
                    @if($user->isEBuddy())
                        <span class="badge badge-purple">Pro E-Buddy</span>
                    @elseif($user->isAdmin())
                        <span class="badge badge-yellow">Admin</span>
                    @else
                        <span class="badge badge-green">Player</span>
                    @endif
                </div>
                <p style="font-size:13px;color:var(--text-2);">Member since {{ $user->created_at->format('M Y') }} · {{ $user->timezone }}</p>
            </div>

            {{-- Action buttons --}}
            <div style="padding-bottom:28px;display:flex;gap:10px;flex-wrap:wrap;flex-shrink:0;">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Edit Profile</a>
                <a href="{{ route('profile.add-game') }}" class="btn btn-ghost btn-sm">Add Game</a>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;margin-top:28px;border-bottom:1px solid var(--border);">
            @php
                $tabs = [
                    ['id' => 'overview', 'label' => 'Overview'],
                    ['id' => 'games',    'label' => 'Games (' . $userProfiles->count() . ')'],
                ];
                if ($user->isEBuddy()) {
                    $tabs[] = ['id' => 'reviews', 'label' => 'Reviews (' . ($ebuddy->reviews->count() ?? 0) . ')'];
                }
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
            @can('access-ebuddy-features')
            <div class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:10px;">About</p>
                <p style="font-size:14px;line-height:1.75;color:var(--text-2);">
                    {{ $ebuddy->bio ?? 'No bio yet — edit your profile to add one.' }}
                </p>
            </div>
            @endcan

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

            @if($user->isEBuddy())
            <div style="margin-top:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <p class="section-title" style="margin:0; display:flex; align-items:center; gap:8px;">
                        Recent Reviews 
                        <span style="font-size:12px; font-weight:500; color:var(--text-3); background:var(--surface2); padding:2px 8px; border-radius:6px; border:1px solid var(--border);">{{ $ebuddy->reviews->count() }}</span>
                    </p>
                    @if($ebuddy->reviews->count() > 3)
                        <a href="?tab=reviews" style="font-size:12px; color:var(--accent-light); font-weight:700; text-decoration:none;">See All</a>
                    @endif
                </div>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @forelse($ebuddy->reviews->sortByDesc('created_at')->take(3) as $review)
                    <div class="card" style="padding:20px; border-color:var(--border);">
                        <div style="display:flex;gap:14px;">
                            <a href="{{ route('browse.show', $review->player->id) }}" style="width:40px;height:40px;border-radius:10px;overflow:hidden;flex-shrink:0;border:1px solid var(--border);text-decoration:none;">
                                <img src="{{ $review->player->avatar ? asset('storage/'.$review->player->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$review->player->name }}" style="width:100%;height:100%;object-fit:cover;">
                            </a>
                            <div style="flex:1;">
                                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                                    <div>
                                        <a href="{{ route('browse.show', $review->player->id) }}" style="font-size:13px;font-weight:800;margin-bottom:2px;color:var(--text);text-decoration:none;display:block;transition:color 0.15s;" onmouseover="this.style.color='var(--accent-light)'" onmouseout="this.style.color='var(--text)'">
                                            {{ $review->player->display_name ?? $review->player->name }}
                                        </a>
                                        <div style="display:flex;gap:2px;">
                                            @foreach($review->starsArray() as $isFilled)
                                                <svg width="12" height="12" fill="{{ $isFilled ? 'var(--yellow)' : 'rgba(255,255,255,0.05)' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span style="font-size:11px;color:var(--text-3);">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p style="font-size:13px;color:var(--text-2);line-height:1.5;">{{ $review->comment ?? 'No comment provided.' }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="card" style="padding:40px 20px;text-align:center;border:1.5px dashed var(--border);background:rgba(255,255,255,0.01);">
                        <p style="color:var(--text-3);font-size:13px;">You haven't received any reviews yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar stats --}}
        @can('access-ebuddy-features')
        <div class="card" style="padding:20px;">
            <p class="section-title" style="margin-bottom:16px;">Stats</p>
            @php $pstats = [
                ['label'=>'Rating',     'value'=> number_format($ebuddy->global_rating,1).' ★', 'color'=>'var(--yellow)'],
                ['label'=>'Earnings',   'value'=> '$'.number_format($ebuddy->getTotalEarnings(),2), 'color'=>'var(--green)'],
                ['label'=>'Completion', 'value'=> $ebuddy->getCompletionRate().'%',               'color'=>'var(--accent-light)'],
                ['label'=>'Sessions',   'value'=> $ebuddy->getSessionCount(),                    'color'=>'var(--text)'],
                ['label'=>'Games',      'value'=> $userProfiles->count(),                        'color'=>'var(--text)'],
            ]; @endphp
            @foreach($pstats as $s)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);">
                <span style="font-size:13px;color:var(--text-2);">{{ $s['label'] }}</span>
                <span style="font-size:14px;font-weight:700;color:{{ $s['color'] }};">{{ $s['value'] }}</span>
            </div>
            @endforeach
        </div>
        @endcan
    </div>

    @elseif($activeTab === 'games')
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;justify-content:flex-end;">
            <a href="{{ route('profile.add-game') }}" class="btn btn-primary btn-sm">Add Game</a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;">
            @forelse($userProfiles as $profile)
            <div class="card" style="padding:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--accent-light);margin-bottom:6px;">{{ $profile->game->title }}</p>
                    <p style="font-size:16px;font-weight:800;color:var(--text);">{{ $profile->currentRank->title ?? 'Unranked' }}</p>
                </div>
                <form action="{{ route('profile.remove-game', $profile) }}" method="POST" onsubmit="return confirm('Remove this game from your library?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                </form>
            </div>
            @empty
            <div style="grid-column:1/-1;padding:56px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;">
                <p style="color:var(--text-2);margin-bottom:18px;font-size:14px;">No games added yet.</p>
                <a href="{{ route('profile.add-game') }}" class="btn btn-primary">Add Your First Game</a>
            </div>
            @endforelse
        </div>
    @elseif($activeTab === 'reviews' && $user->isEBuddy())
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:8px;">
            <div>
                <h2 style="font-size:1.2rem;font-weight:800;margin-bottom:4px;">My Reviews</h2>
                <p style="font-size:13px;color:var(--text-2);">Feedback received from players you've helped.</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:12px;font-weight:600;color:var(--text-3);">Sort by:</span>
                <select onchange="window.location.href=this.value" style="padding:8px 12px;background:var(--surface2);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:12px;font-weight:600;outline:none;cursor:pointer;">
                    <option value="?tab=reviews&sort=newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="?tab=reviews&sort=highest" {{ request('sort') === 'highest' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="?tab=reviews&sort=lowest" {{ request('sort') === 'lowest' ? 'selected' : '' }}>Lowest Rated</option>
                </select>
            </div>
        </div>

        @php
            $reviews = $ebuddy->reviews;
            $sort = request('sort', 'newest');
            if ($sort === 'highest') {
                $reviews = $reviews->sortByDesc('rating');
            } elseif ($sort === 'lowest') {
                $reviews = $reviews->sortBy('rating');
            } else {
                $reviews = $reviews->sortByDesc('created_at');
            }
        @endphp

        @forelse($reviews as $review)
        <div class="card" style="padding:24px;">
            <div style="display:flex;gap:16px;">
                <a href="{{ route('browse.show', $review->player->id) }}" style="width:48px;height:48px;border-radius:12px;overflow:hidden;flex-shrink:0;border:1px solid var(--border);text-decoration:none;">
                    <img src="{{ $review->player->avatar ? asset('storage/'.$review->player->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$review->player->name }}" style="width:100%;height:100%;object-fit:cover;">
                </a>
                <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                        <div>
                            <a href="{{ route('browse.show', $review->player->id) }}" style="font-size:14px;font-weight:800;margin-bottom:2px;color:var(--text);text-decoration:none;display:block;transition:color 0.15s;" onmouseover="this.style.color='var(--accent-light)'" onmouseout="this.style.color='var(--text)'">
                                {{ $review->player->display_name ?? $review->player->name }}
                            </a>
                            <div style="display:flex;gap:2px;">
                                @foreach($review->starsArray() as $isFilled)
                                    <svg width="14" height="14" fill="{{ $isFilled ? 'var(--yellow)' : 'rgba(255,255,255,0.05)' }}" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path></svg>
                                @endforeach
                            </div>
                        </div>
                        <span style="font-size:12px;color:var(--text-3);">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <p style="font-size:14px;color:var(--text-2);line-height:1.6;">{{ $review->comment ?? 'No comment provided.' }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="card" style="padding:60px 20px;text-align:center;border:1.5px dashed var(--border);background:rgba(255,255,255,0.01);">
            <p style="color:var(--text-3);font-size:14px;">You haven't received any reviews yet.</p>
        </div>
        @endforelse
    </div>
    @endif
</div>
@endsection

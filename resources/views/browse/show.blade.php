@extends($layout, ['title' => $user->display_name ?? $user->name])

@section('content')

{{-- Profile Banner --}}
<div style="position:relative;margin:-32px -24px 0;background:linear-gradient(135deg,#1a1040 0%,#0b0c16 40%,#130b2e 100%);overflow:hidden;">
    @if($user->eBuddy && $user->eBuddy->banner)
        <img src="{{ asset('storage/'.$user->eBuddy->banner) }}" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;pointer-events:none;">
        <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(to top, var(--bg) 0%, transparent 100%);pointer-events:none;"></div>
    @else
        <div style="position:absolute;top:-60px;left:10%;width:500px;height:500px;background:radial-gradient(circle,rgba(124,58,237,0.25) 0%,transparent 65%);pointer-events:none;"></div>
        <div style="position:absolute;top:-40px;right:5%;width:400px;height:400px;background:radial-gradient(circle,rgba(157,95,245,0.12) 0%,transparent 65%);pointer-events:none;"></div>
    @endif

    <div style="position:relative;padding:40px 24px 0;">
        <div style="display:flex;align-items:flex-end;gap:20px;flex-wrap:wrap;">

            {{-- Avatar --}}
            <div style="flex-shrink:0;position:relative;margin-bottom:-28px;">
                <div style="width:100px;height:100px;border-radius:50%;overflow:hidden;border:4px solid var(--bg);box-shadow: 0 8px 24px rgba(0,0,0,0.5);">
                    <img src="{{ $user->avatar ? asset('storage/'.$user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
            </div>

            {{-- Name --}}
            <div style="padding-bottom:28px;min-width:0;flex:1;">
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:5px;">
                    <h1 style="font-size:1.5rem;font-weight:800;letter-spacing:-0.03em;">{{ $user->display_name ?? $user->name }}</h1>
                    @if($user->isEBuddy())
                        <span class="badge badge-purple">Pro E-Buddy</span>
                    @else
                        <span class="badge" style="background:rgba(255,255,255,0.05);color:var(--text-2);border:1px solid var(--border);">Player</span>
                    @endif
                </div>
                <p style="font-size:13px;color:var(--text-2);">Member since {{ $user->created_at?->format('M Y') ?? 'Recently' }}</p>
            </div>

            {{-- Actions --}}
            <div style="padding-bottom:28px;flex-shrink:0;display:flex;gap:10px;align-items:center;">
                @can('create', [App\Models\Report::class, $user])
                <div x-data="{ open: false }">
                    <button @click="open = true" class="btn btn-ghost btn-sm" style="color:var(--red);border-color:rgba(239,68,68,0.2);">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Report
                    </button>

                    {{-- Report Modal --}}
                    <template x-teleport="body">
                        <div x-show="open" x-cloak>
                            {{-- Backdrop --}}
                            <div style="position:fixed; inset:0; background:rgba(0,0,0,0.85); backdrop-filter:blur(10px); z-index:99998;" @click="open=false"></div>
                            {{-- Modal Card --}}
                            <div style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:99999; background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:32px; width:90%; max-width:440px;">
                                <h3 style="font-size:1.2rem;font-weight:900;margin-bottom:8px;letter-spacing:-0.02em;">Report User</h3>
                                <p style="font-size:13px;color:var(--text-2);margin-bottom:24px;">Tell us what's wrong with <strong>{{ $user->display_name ?? $user->name }}</strong>.</p>
                                
                                <form action="{{ route('report.store') }}" method="POST" style="display:flex;flex-direction:column;gap:18px;">
                                    @csrf
                                    <input type="hidden" name="reported_id" value="{{ $user->id }}">
                                    
                                    <div class="form-group">
                                        <label class="form-label">Reason for report</label>
                                        <textarea name="reason" class="form-input" rows="4" required placeholder="Describe the issue in detail..."></textarea>
                                    </div>

                                    <div style="display:flex;gap:12px;padding-top:6px;">
                                        <button type="button" @click="open=false" class="btn btn-ghost" style="flex:1;">Cancel</button>
                                        <button type="submit" class="btn btn-primary" style="flex:1;background:var(--red);border-color:var(--red);">Submit Report</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
                @else
                    @if(auth()->id() !== $user->id)
                        <div class="btn btn-ghost btn-sm" style="color:var(--text-3);border-color:var(--border);cursor:default;opacity:0.7;">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"></path></svg>
                            Report Pending
                        </div>
                    @endif
                @endcan

                <a href="{{ route('browse.index') }}" class="btn btn-ghost btn-sm">← Back</a>
            </div>
        </div>

        {{-- Tabs --}}
        <div style="display:flex;margin-top:28px;border-bottom:1px solid var(--border);">
            @php $activeTab = request('tab', 'overview'); @endphp
            
            <a href="?tab=overview" style="padding:12px 20px;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:-1px;border-bottom:2px solid {{ $activeTab === 'overview' ? 'var(--accent-light)' : 'transparent' }};color:{{ $activeTab === 'overview' ? 'var(--text)' : 'var(--text-2)' }};transition:color 0.15s;">
                Overview
            </a>

            @if($user->isEBuddy())
            <a href="?tab=services" style="padding:12px 20px;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:-1px;border-bottom:2px solid {{ $activeTab === 'services' ? 'var(--accent-light)' : 'transparent' }};color:{{ $activeTab === 'services' ? 'var(--text)' : 'var(--text-2)' }};transition:color 0.15s;">
                Services ({{ $user->eBuddy->services->count() }})
            </a>
            <a href="?tab=reviews" style="padding:12px 20px;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:-1px;border-bottom:2px solid {{ $activeTab === 'reviews' ? 'var(--accent-light)' : 'transparent' }};color:{{ $activeTab === 'reviews' ? 'var(--text)' : 'var(--text-2)' }};transition:color 0.15s;">
                Reviews ({{ $user->eBuddy->reviews->count() }})
            </a>
            @endif

            <a href="?tab=chat" style="padding:12px 20px;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:-1px;border-bottom:2px solid {{ $activeTab === 'chat' ? 'var(--accent-light)' : 'transparent' }};color:{{ $activeTab === 'chat' ? 'var(--text)' : 'var(--text-2)' }};transition:color 0.15s;">
                Chat
            </a>
        </div>
    </div>
</div>

{{-- Tab Content --}}
<div style="padding-top:32px;">

    @if($activeTab === 'overview')
    <div class="side-col">
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:10px;">About</p>
                <p style="font-size:14px;line-height:1.75;color:var(--text-2);">{{ $user->eBuddy?->bio ?? 'No bio provided.' }}</p>
            </div>

            @if($user->isEBuddy())
            <div style="margin-top:8px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <p class="section-title" style="margin:0; display:flex; align-items:center; gap:8px;">
                        Recent Reviews 
                        <span style="font-size:12px; font-weight:500; color:var(--text-3); background:var(--surface2); padding:2px 8px; border-radius:6px; border:1px solid var(--border);">{{ $user->eBuddy->reviews->count() }}</span>
                    </p>
                    @if($user->eBuddy->reviews->count() > 3)
                        <a href="?tab=reviews" style="font-size:12px; color:var(--accent-light); font-weight:700; text-decoration:none;">See All</a>
                    @endif
                </div>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    @forelse($user->eBuddy->reviews->sortByDesc('created_at')->take(3) as $review)
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
                        <p style="color:var(--text-3);font-size:13px;">This E-Buddy hasn't received any reviews yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
            
            @if($user->gameProfiles->count())
            <div class="card" style="padding:24px;">
                <p class="section-title" style="margin-bottom:16px;">Games Played</p>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;">
                    @foreach($user->gameProfiles as $gp)
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
            <p class="section-title" style="margin-bottom:16px;">Quick Stats</p>
            <div style="display:flex;flex-direction:column;gap:12px;">
                @if($user->isEBuddy())
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);">
                        <span style="font-size:13px;color:var(--text-2);">Rating</span>
                        <span style="font-size:14px;font-weight:700;color:var(--yellow);">{{ number_format($user->eBuddy->global_rating ?? 0, 1) }} ★</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);">
                        <span style="font-size:13px;color:var(--text-2);">Completion</span>
                        <span style="font-size:14px;font-weight:700;color:var(--green);">{{ $user->eBuddy->getCompletionRate() }}%</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);">
                        <span style="font-size:13px;color:var(--text-2);">Sessions</span>
                        <span style="font-size:14px;font-weight:700;color:var(--accent-light);">{{ $user->eBuddy->getSessionCount() }}</span>
                    </div>
                @endif
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;border-bottom:1px solid var(--border);">
                    <span style="font-size:13px;color:var(--text-2);">Games</span>
                    <span style="font-size:14px;font-weight:700;color:var(--text);">{{ $user->gameProfiles->count() }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:11px 0;">
                    <span style="font-size:13px;color:var(--text-2);">Role</span>
                    <span style="font-size:14px;font-weight:700;color:var(--accent-light);">{{ ucfirst($user->role->title) }}</span>
                </div>
            </div>
            @if($user->isEBuddy())
                <a href="?tab=services" class="btn btn-primary" style="width:100%;margin-top:16px;justify-content:center;">Book a Session</a>
            @endif
        </div>
    </div>

    @elseif($activeTab === 'services' && $user->isEBuddy())
    <div style="display:flex;flex-direction:column;gap:14px;">
        @forelse($user->eBuddy->services as $service)
        <div class="card" style="padding:24px;" x-data="{ open: false }">
            {{-- Service Card Content (same as before but using $user) --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:20px;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <p style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--accent-light);margin-bottom:5px;">{{ $service->game->title }}</p>
                    <h3 style="font-size:15px;font-weight:700;margin-bottom:8px;">{{ $service->title }}</h3>
                    <p style="font-size:13px;color:var(--text-2);line-height:1.6;margin-bottom:8px;">{{ $service->description }}</p>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:12px;flex-shrink:0;">
                    <p style="font-size:1.4rem;font-weight:800;color:var(--text);">${{ number_format($service->price, 0) }}<span style="font-size:12px;font-weight:500;color:var(--text-2);">/hr</span></p>
                    @if(auth()->id() !== $user->id)
                        <button @click="open=true" class="btn btn-primary btn-sm">Book Now</button>
                    @endif
                </div>
            </div>

            {{-- Booking Modal --}}
            @if(auth()->id() !== $user->id)
            <template x-teleport="body">
                <div x-show="open" x-cloak>
                    {{-- Backdrop --}}
                    <div style="position:fixed; inset:0; background:rgba(0,0,0,0.75); backdrop-filter:blur(10px); z-index:99998;" @click="open=false"></div>
                    {{-- Modal Card --}}
                    <div style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:99999; background:var(--surface); border:1px solid var(--border); border-radius:20px; padding:32px; width:90%; max-width:420px;">
                        <h3 style="font-size:1.1rem;font-weight:800;margin-bottom:4px;">{{ $service->title }}</h3>
                        <p style="font-size:13px;color:var(--text-2);margin-bottom:22px;">with {{ $user->display_name ?? $user->name }} · ${{ number_format($service->price, 0) }}/hr</p>
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
            </template>
            @endif
        </div>
        @empty
            <p>No services.</p>
        @endforelse
    </div>

    @elseif($activeTab === 'reviews' && $user->isEBuddy())
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;gap:20px;flex-wrap:wrap;margin-bottom:8px;">
            <div>
                <h2 style="font-size:1.2rem;font-weight:800;margin-bottom:4px;">All Reviews</h2>
                <p style="font-size:13px;color:var(--text-2);">What players are saying about {{ $user->display_name ?? $user->name }}</p>
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
            $reviews = $user->eBuddy->reviews;
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
            <p style="color:var(--text-3);font-size:14px;">This E-Buddy hasn't received any reviews yet.</p>
        </div>
        @endforelse
    </div>

    @elseif($activeTab === 'chat')
    <div style="max-width:800px;margin:0 auto;">
        <div class="card" style="padding:48px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;background:var(--surface);">
            <h3 style="font-size:1.2rem;font-weight:800;margin-bottom:8px;">Direct Message</h3>
            <p style="color:var(--text-2);font-size:14px;max-width:400px;margin:0 auto 24px;">Start a private conversation with {{ $user->display_name ?? $user->name }}.</p>
            <form action="{{ route('chat.start', $user->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Open Chat Room</button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

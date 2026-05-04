@extends('layouts.dashboard', ['title' => 'Add Game'])

@section('content')
<div style="max-width:800px;" x-data="{
    selectedGameId: '',
    games: {{ $games->toJson() }},
    get selectedGame() {
        return this.games.find(g => g.id == this.selectedGameId) || null;
    },
    get ranks() {
        return this.selectedGame ? this.selectedGame.ranks : [];
    }
}">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.3rem;font-weight:800;letter-spacing:-0.02em;">Add Game to Library</h1>
            <p style="font-size:13px;color:var(--text-2);margin-top:4px;">Select a game and set your current competitive rank.</p>
        </div>
        <a href="{{ route('ebuddy.profile') }}" class="btn btn-ghost btn-sm">Cancel</a>
    </div>

    <form action="{{ route('ebuddy.profile.store-game') }}" method="POST">
        @csrf

        {{-- Game Selection --}}
        <div class="card" style="padding:28px;margin-bottom:14px;">
            <p class="section-title" style="margin-bottom:18px;">Choose a Game</p>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
                @foreach($games as $game)
                    @php $inLibrary = in_array($game->id, $existingGameIds); @endphp
                    <label style="cursor:{{ $inLibrary ? 'not-allowed' : 'pointer' }};display:block;opacity:{{ $inLibrary ? '0.45' : '1' }};">
                        <input type="radio" name="game_id" value="{{ $game->id }}"
                               x-model="selectedGameId"
                               class="hidden"
                               {{ $inLibrary ? 'disabled' : '' }}>

                        {{-- Game Card with Cover --}}
                        <div :style="selectedGameId == '{{ $game->id }}' 
                                ? 'border-color:var(--accent);box-shadow:0 0 0 3px rgba(124,58,237,0.2);' 
                                : ''"
                             style="border:1.5px solid var(--border-2);border-radius:14px;overflow:hidden;transition:all 0.15s;background:var(--surface2);">

                            {{-- Cover Image --}}
                            <div style="height:90px;overflow:hidden;position:relative;background:linear-gradient(135deg,#1a1040,#130b2e);">
                                @if($game->image)
                                    <img src="{{ asset('storage/'.$game->image) }}" 
                                         style="width:100%;height:100%;object-fit:cover;opacity:0.85;">
                                @else
                                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                        <div style="width:40px;height:40px;border-radius:10px;background:rgba(124,58,237,0.15);display:flex;align-items:center;justify-content:center;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(157,95,245,0.6)" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                                        </div>
                                    </div>
                                @endif
                                {{-- Selected overlay --}}
                                <div x-show="selectedGameId == '{{ $game->id }}'" 
                                     style="position:absolute;inset:0;background:rgba(124,58,237,0.2);display:flex;align-items:center;justify-content:center;">
                                    <div style="width:28px;height:28px;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Label --}}
                            <div style="padding:10px 12px;">
                                <p style="font-size:12px;font-weight:700;color:var(--text);line-height:1.3;"
                                   :style="selectedGameId == '{{ $game->id }}' ? 'color:var(--accent-light);' : ''">
                                    {{ $game->title }}
                                </p>
                                @if($inLibrary)
                                    <p style="font-size:10px;font-weight:600;color:var(--text-3);margin-top:2px;text-transform:uppercase;letter-spacing:0.08em;">Added</p>
                                @endif
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('game_id')
                <p style="font-size:11px;color:var(--red);margin-top:10px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Rank Selection (shown only after game selected) --}}
        <div class="card" style="padding:28px;margin-bottom:20px;" x-show="selectedGameId" x-transition>
            <p class="section-title" style="margin-bottom:6px;">Select Your Rank</p>
            <p style="font-size:13px;color:var(--text-2);margin-bottom:18px;" x-text="'Your current competitive rank in ' + (selectedGame ? selectedGame.title : '')"></p>

            <div class="form-group" style="max-width:360px;">
                <label class="form-label" for="rank_id">Current Rank</label>
                <select class="form-input" id="rank_id" name="rank_id">
                    <option value="">-- Select rank --</option>
                    <template x-for="rank in ranks" :key="rank.id">
                        <option :value="rank.id" x-text="rank.title"></option>
                    </template>
                </select>
                @error('rank_id')
                    <p style="font-size:11px;color:var(--red);margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:10px;" x-show="selectedGameId" x-transition>
            <a href="{{ route('ebuddy.profile') }}" class="btn btn-ghost">Cancel</a>
            <button type="submit" class="btn btn-primary">Add to Library</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.dashboard', ['title' => 'Services'])

@section('content')
<div style="display:flex;flex-direction:column;gap:20px;" x-data="{ showForm: false }">

    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:1.4rem;font-weight:800;letter-spacing:-0.02em;">My Services</h1>
            <p style="font-size:13px;color:var(--text-2);margin-top:3px;">Create and manage the gaming sessions you offer.</p>
        </div>
        <button @click="showForm = !showForm" class="btn btn-primary">
            <span x-text="showForm ? 'Cancel' : 'Create Service'"></span>
        </button>
    </div>

    {{-- Add Service Form --}}
    <div x-show="showForm" x-transition class="card" style="padding:28px;">
        <p class="section-title" style="margin-bottom:20px;">New Service</p>
        <form action="{{ route('ebuddy.services.store') }}" method="POST">
            @csrf
            <div style="display:grid;grid-template-columns:1fr;gap:16px;">
                <div class="form-group">
                    <label class="form-label">Game</label>
                    @if($games->isEmpty())
                        <select name="game_id" class="form-input" disabled style="background: var(--surface2); color: var(--text-2);">
                            <option value="">No games in your library</option>
                        </select>
                        <small style="color:var(--text-2);margin-top:8px;display:block;">
                            <a href="{{ route('ebuddy.profile.add-game') }}" style="color:var(--primary);text-decoration:none;font-weight:600;">+ Add a game to your library</a> first.
                        </small>
                    @else
                        <select name="game_id" class="form-input">
                            @foreach($games as $game)
                                <option value="{{ $game->id }}">{{ $game->title }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-input" placeholder="e.g. Diamond ranked coaching session" required {{ $games->isEmpty() ? 'disabled' : '' }}>
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" rows="3" placeholder="Describe your session..." required {{ $games->isEmpty() ? 'disabled' : '' }}></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Price per hour ($)</label>
                    <input type="number" step="0.01" name="price" class="form-input" placeholder="25.00" required {{ $games->isEmpty() ? 'disabled' : '' }}>
                </div>
                <div style="display:flex;align-items:flex-end;">
                    <button type="submit" class="btn btn-primary" style="width:100%;" {{ $games->isEmpty() ? 'disabled' : '' }}>Publish Service</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Services Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;">
        @forelse($services as $service)
        <div class="card" style="display:flex;flex-direction:column;overflow:hidden;">
            <div style="padding:20px 22px;border-bottom:1px solid var(--border);background:linear-gradient(135deg,var(--surface2),rgba(124,58,237,0.05));">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;">
                    <div>
                        <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--accent-light);margin-bottom:5px;">{{ $service->game->title }}</p>
                        <h3 style="font-size:15px;font-weight:700;color:var(--text);line-height:1.3;">{{ $service->title }}</h3>
                    </div>
                    <form action="{{ route('ebuddy.services.destroy', $service) }}" method="POST" onsubmit="return confirm('Delete this service?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="padding:5px 10px;font-size:11px;flex-shrink:0;">Delete</button>
                    </form>
                </div>
            </div>
            <div style="padding:18px 22px;flex:1;">
                <p style="font-size:13px;color:var(--text-2);line-height:1.6;margin-bottom:16px;">{{ Str::limit($service->description, 100) }}</p>
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--text-2);">Rank: <strong style="color:var(--text);">{{ $service->rank ? $service->rank->title : 'Unranked' }}</strong></span>
                    <span style="font-size:1.2rem;font-weight:800;color:var(--text);">${{ number_format($service->price, 0) }}<span style="font-size:11px;font-weight:500;color:var(--text-2);">/hr</span></span>
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;padding:60px;text-align:center;border:1.5px dashed var(--border);border-radius:16px;">
            <p style="color:var(--text-2);margin-bottom:16px;font-size:14px;">No services yet. Create your first one!</p>
            <button @click="showForm = true" class="btn btn-primary">Create Service</button>
        </div>
        @endforelse
    </div>
</div>
@endsection

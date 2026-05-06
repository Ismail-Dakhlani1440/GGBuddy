@extends('layouts.admin', ['title' => 'Edit ' . $game->title])

@section('content')
<div style="max-width:1100px;">
    
    <div style="margin-bottom:48px;">
        <a href="{{ route('admin.games.index') }}" style="color:var(--text-3); text-decoration:none; font-size:14px; font-weight:700; display:flex; align-items:center; gap:8px; margin-bottom:16px; transition:0.2s;" onmouseover="this.style.color='var(--text-2)'" onmouseout="this.style.color='var(--text-3)'">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
            Back to Library
        </a>
        <h1 style="font-size:2.8rem; font-weight:900; letter-spacing:-0.05em; background:linear-gradient(to right, #fff, var(--accent-light)); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">Configure Game</h1>
    </div>

    @if ($errors->any())
        <div style="background:rgba(244, 63, 94, 0.1); border:1px solid rgba(244, 63, 94, 0.2); padding:24px; border-radius:24px; margin-bottom:40px; color:#fb7185; font-size:14px;">
            <p style="font-weight:900; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Sync Errors Detected:
            </p>
            <ul style="list-style:inside; opacity:0.9;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.games.update', $game->id) }}" method="POST" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:40px;">
        @csrf
        @method('PUT')
        
        <div style="display:grid; grid-template-columns:1.6fr 1fr; gap:40px; align-items:start;">
            {{-- Left Side: Game Info --}}
            <div class="card" style="padding:40px; border-radius:32px;">
                <div style="margin-bottom:32px;">
                    <h3 style="font-weight:900; font-size:1.4rem; margin-bottom:8px;">Core Information</h3>
                    <p style="font-size:15px; color:var(--text-2);">Basic details that identify the game across the platform.</p>
                </div>
                
                <div style="display:flex; flex-direction:column; gap:32px;">
                    <div class="form-group">
                        <label class="form-label" style="font-weight:800; margin-bottom:12px;">Game Title</label>
                        <input type="text" name="title" value="{{ $game->title }}" class="form-input" required placeholder="e.g. League of Legends" style="padding:16px 24px; border-radius:16px;">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="font-weight:800; margin-bottom:12px;">Description</label>
                        <textarea name="description" class="form-input" rows="8" placeholder="Provide a detailed overview of mechanics, competitive nature, and platform..." style="padding:16px 24px; border-radius:16px;">{{ $game->description }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Right Side: Cover --}}
            <div class="card" style="padding:40px; border-radius:32px;">
                <div style="margin-bottom:32px;">
                    <h3 style="font-weight:900; font-size:1.4rem; margin-bottom:8px;">Visual Assets</h3>
                    <p style="font-size:15px; color:var(--text-2);">The primary image shown in the catalog.</p>
                </div>
                
                <div x-data="{ preview: '{{ $game->cover ? Storage::disk('public')->url($game->cover) : 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&q=80&w=800' }}' }" style="display:flex; flex-direction:column; gap:28px;">
                    <div style="aspect-ratio:16/9; background:#000; border:2px dashed var(--border); border-radius:24px; overflow:hidden; display:flex; align-items:center; justify-content:center; position:relative; transition:0.3s;" :style="preview ? 'border-style:solid' : ''">
                        <template x-if="preview">
                            <img :src="preview" style="width:100%; height:100%; object-fit:cover;">
                        </template>
                        <template x-if="!preview">
                            <div style="text-align:center; color:var(--text-3); padding:40px;">
                                <div style="width:56px; height:56px; background:rgba(255,255,255,0.02); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                                    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <p style="font-size:13px; font-weight:800; text-transform:uppercase; letter-spacing:0.1em;">Awaiting Upload</p>
                            </div>
                        </template>
                    </div>
                    
                    <div style="position:relative;">
                        <input type="file" name="cover" @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }" class="form-input" style="width:100%; padding:14px; cursor:pointer; border-radius:16px;">
                    </div>
                    <p style="font-size:13px; color:var(--text-3); line-height:1.6;">Leave empty to keep the current cover image.</p>
                </div>
            </div>
        </div>

        {{-- Ranks --}}
        <div x-data="{ ranks: @js($game->ranks) }" class="card" style="padding:40px; border-radius:32px;">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:40px;">
                <div>
                    <h3 style="font-weight:900; font-size:1.6rem; margin-bottom:8px;">Competitive Ecosystem</h3>
                    <p style="font-size:15px; color:var(--text-2);">Update or expand the ranking tiers for this game title.</p>
                </div>
                <button type="button" @click="ranks.push({ title: '', tier: ranks.length + 1 })" class="btn btn-primary" style="padding:12px 24px; font-size:14px; border-radius:16px; background:rgba(139, 92, 246, 0.15); color:var(--accent-light); border:1px solid rgba(139, 92, 246, 0.2); box-shadow:none;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="margin-right:8px;"><path d="M12 4v16m8-8H4"></path></svg>
                    Expand Tiers
                </button>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:24px;">
                <template x-for="(rank, index) in ranks" :key="index">
                    <div style="display:flex; flex-direction:column; gap:20px; padding:28px; background:rgba(0,0,0,0.25); border:1px solid var(--border); border-radius:28px; position:relative; transition:0.3s;" onmouseover="this.style.borderColor='var(--accent-light)'" onmouseout="this.style.borderColor='var(--border)'">
                        <template x-if="rank.id">
                            <input type="hidden" :name="'ranks['+index+'][id]'" :value="rank.id">
                        </template>
                        
                        <div class="form-group">
                            <label class="form-label" style="font-weight:800; font-size:13px; text-transform:uppercase; letter-spacing:0.05em;">Tier Title</label>
                            <input type="text" :name="'ranks['+index+'][title]'" x-model="rank.title" class="form-input" required placeholder="e.g. Master" style="padding:14px 20px; border-radius:14px;">
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-weight:800; font-size:13px; text-transform:uppercase; letter-spacing:0.05em;">Progression Value</label>
                            <input type="number" :name="'ranks['+index+'][tier]'" x-model="rank.tier" class="form-input" required style="padding:14px 20px; border-radius:14px;">
                        </div>

                        <div class="form-group">
                            <label class="form-label" style="font-weight:800; font-size:13px; text-transform:uppercase; letter-spacing:0.05em;">Tier Icon</label>
                            <div style="display:flex; align-items:center; gap:12px;">
                                <template x-if="rank.icon">
                                    <img :src="'/storage/' + rank.icon" style="width:40px; height:40px; border-radius:8px; object-fit:contain; background:rgba(0,0,0,0.3); padding:4px;">
                                </template>
                                <input type="file" :name="'ranks['+index+'][icon]'" class="form-input" style="flex:1; padding:10px; border-radius:12px; font-size:12px;">
                            </div>
                        </div>

                        <template x-if="rank.id">
                            <button type="button" 
                                    @click="if(confirm('Delete this rank tier? This action cannot be undone.')) { $refs.deleteRankForm.action = '/admin/ranks/' + rank.id; $refs.deleteRankForm.submit(); }"
                                    style="position:absolute; top:16px; right:16px; background:rgba(244, 63, 94, 0.1); border:1px solid rgba(244, 63, 94, 0.2); color:var(--red); padding:8px; border-radius:12px; cursor:pointer; transition:0.2s;" onmouseover="this.style.background='rgba(244, 63, 94, 0.2)'" onmouseout="this.style.background='rgba(244, 63, 94, 0.1)'">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </template>
                        <template x-if="!rank.id">
                            <button type="button" @click="ranks.splice(index, 1)" style="position:absolute; top:16px; right:16px; background:rgba(244, 63, 94, 0.1); border:1px solid rgba(244, 63, 94, 0.2); color:var(--red); padding:8px; border-radius:12px; cursor:pointer; transition:0.2s;" onmouseover="this.style.background='rgba(244, 63, 94, 0.2)'" onmouseout="this.style.background='rgba(244, 63, 94, 0.1)'">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        <div style="display:flex; gap:24px; padding-top:20px;">
            <button type="submit" class="btn btn-primary" style="flex:2; padding:20px; font-size:18px; border-radius:24px; font-weight:900;">
                Update Database Record
            </button>
            <a href="{{ route('admin.games.index') }}" class="btn btn-ghost" style="flex:1; padding:20px; font-size:18px; border-radius:24px; font-weight:900; text-align:center;">
                Cancel Changes
            </a>
        </div>
    </form>

    {{-- Hidden Delete Form for Ranks (Moved Outside Main Form) --}}
    <form x-ref="deleteRankForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection

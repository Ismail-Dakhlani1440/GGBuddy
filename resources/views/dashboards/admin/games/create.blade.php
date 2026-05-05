@extends('layouts.admin', ['title' => 'Add New Game'])

@section('content')
<div style="max-width:1000px;">
    
    <div style="margin-bottom:32px;">
        <a href="{{ route('admin.games.index') }}" style="color:var(--text-3);text-decoration:none;font-size:13px;display:flex;align-items:center;gap:6px;margin-bottom:12px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"></path></svg>
            Back to Library
        </a>
        <h1 style="font-size:1.5rem;font-weight:900;letter-spacing:-0.03em;">Add New Game</h1>
    </div>

    <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:32px;">
        @csrf
        
        <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:32px;align-items:start;">
            {{-- Left Side: Game Info --}}
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:24px;padding:32px;display:flex;flex-direction:column;gap:24px;">
                <h3 style="font-weight:900;font-size:1.1rem;margin-bottom:8px;">Game Information</h3>
                
                <div class="form-group">
                    <label class="form-label">Game Title</label>
                    <input type="text" name="title" class="form-input" required placeholder="e.g. Valorant">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" rows="6" placeholder="What is this game about?"></textarea>
                </div>
            </div>

            {{-- Right Side: Cover --}}
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:24px;padding:32px;display:flex;flex-direction:column;gap:24px;">
                <h3 style="font-weight:900;font-size:1.1rem;margin-bottom:8px;">Game Cover</h3>
                
                <div x-data="{ preview: null }" style="display:flex;flex-direction:column;gap:16px;">
                    <div style="aspect-ratio:16/9;background:#111;border:2px dashed var(--border);border-radius:16px;overflow:hidden;display:flex;align-items:center;justify-content:center;position:relative;">
                        <template x-if="preview">
                            <img :src="preview" style="width:100%;height:100%;object-fit:cover;">
                        </template>
                        <template x-if="!preview">
                            <div style="text-align:center;color:var(--text-3);">
                                <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-bottom:8px;"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p style="font-size:11px;font-weight:700;text-transform:uppercase;">No Image Selected</p>
                            </div>
                        </template>
                    </div>
                    
                    <input type="file" name="cover" @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }" class="form-input" style="padding:10px;">
                    <p style="font-size:12px;color:var(--text-3);">800x450px recommended (16:9 ratio).</p>
                </div>
            </div>
        </div>

        {{-- Ranks --}}
        <div x-data="{ ranks: [{ title: '', tier: 1 }] }" style="background:var(--surface);border:1px solid var(--border);border-radius:24px;padding:32px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <div>
                    <h3 style="font-weight:900;font-size:1.1rem;">Ranking System</h3>
                    <p style="font-size:13px;color:var(--text-3);">Configure the tiers and titles for this game.</p>
                </div>
                <button type="button" @click="ranks.push({ title: '', tier: ranks.length + 1 })" class="btn btn-ghost btn-sm" style="font-size:12px;font-weight:800;color:var(--accent-light);background:rgba(124,58,237,0.1);padding:8px 16px;">
                    + Add New Rank
                </button>
            </div>

            <div style="display:flex;flex-direction:column;gap:16px;">
                <template x-for="(rank, index) in ranks" :key="index">
                    <div style="display:flex;gap:12px;align-items:flex-end;padding:16px;background:rgba(255,255,255,0.02);border:1px solid var(--border);border-radius:16px;">
                        <div class="form-group" style="flex:3;">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:var(--text-3);letter-spacing:0.05em;">Rank Name</label>
                            <input type="text" :name="'ranks['+index+'][title]'" class="form-input" required placeholder="e.g. Diamond IV">
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label" style="font-size:11px;text-transform:uppercase;color:var(--text-3);letter-spacing:0.05em;">Tier</label>
                            <input type="number" :name="'ranks['+index+'][tier]'" x-model="rank.tier" class="form-input" required>
                        </div>
                        <button type="button" @click="ranks.splice(index, 1)" class="btn btn-ghost" style="padding:10px;color:var(--red);border-radius:12px;background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <div style="display:flex;gap:16px;padding-top:16px;border-top:1px solid var(--border);">
            <button type="submit" class="btn btn-primary" style="flex:1;padding:16px;font-weight:900;font-size:16px;border-radius:16px;">Create Game</button>
            <a href="{{ route('admin.games.index') }}" class="btn btn-ghost" style="flex:1;padding:16px;font-weight:900;font-size:16px;text-align:center;border-radius:16px;border:1px solid var(--border);">Cancel</a>
        </div>
    </form>
</div>
@endsection

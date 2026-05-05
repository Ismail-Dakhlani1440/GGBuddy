@extends('layouts.dashboard', ['title' => 'Messages'])

@section('content')
<div style="display:grid;grid-template-columns:350px 1fr;gap:24px;height:calc(100vh - 120px);max-height:800px;">
    
    {{-- Left Sidebar: Chat List --}}
    <div class="card" style="padding:20px 10px;display:flex;flex-direction:column;overflow-y:auto;background:rgba(255,255,255,0.01);">
        <livewire:chat.chat-list />
    </div>

    {{-- Main Area: Chat Box --}}
    <div style="display:flex;flex-direction:column;min-width:0;">
        @if($activeRoom)
            <livewire:chat.chat-box :roomId="$activeRoom->id" :key="$activeRoom->id" />
        @else
            <div class="card" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:48px;background:rgba(255,255,255,0.01);">
                <div style="width:80px;height:80px;border-radius:50%;background:rgba(124,58,237,0.1);color:var(--accent-light);display:flex;align-items:center;justify-content:center;margin-bottom:20px;">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <h2 style="font-size:1.5rem;font-weight:800;margin-bottom:12px;">Your Inbox</h2>
                <p style="color:var(--text-2);max-width:320px;line-height:1.6;">Select a conversation from the list to start chatting with your E-Buddy or Player.</p>
            </div>
        @endif
    </div>
</div>
@endsection

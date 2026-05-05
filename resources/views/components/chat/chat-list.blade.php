<?php

use Livewire\Component;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public function getListeners()
    {
        return [
            "echo-private:App.Models.User." . auth()->id() . ",MessageSent" => '$refresh',
            "echo-private:App.Models.User." . auth()->id() . ",MessageRead" => '$refresh',
        ];
    }

    public function getRoomsProperty()
    {
        return ChatRoom::where('player_id', Auth::id())
            ->orWhere('e_buddy_id', Auth::id())
            ->whereHas('messages')
            ->with(['player', 'eBuddy', 'latestMessage'])
            ->get()
            ->sortByDesc(function($room) {
                return $room->latestMessage->first()?->sent_at ?? $room->created_at;
            });
    }
};
?>

<div style="display:flex;flex-direction:column;gap:12px;">
    <h2 style="font-size:18px;font-weight:900;margin-bottom:10px;padding:0 10px;letter-spacing:-0.02em;">Messages</h2>
    
    @forelse($this->rooms as $room)
        @php 
            $otherUser = $room->player_id === auth()->id() ? $room->eBuddy : $room->player;
            $isActive = request()->route('roomId') == $room->id;
            $latestMsg = $room->latestMessage->first();
            $hasUnread = $latestMsg && $latestMsg->sender_id !== auth()->id() && is_null($latestMsg->read_at);
        @endphp
        <a href="{{ route('chat', $room->id) }}" style="text-decoration:none;display:flex;align-items:center;gap:14px;padding:14px;border-radius:16px;transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);position:relative;
            {{ $isActive ? 'background:rgba(124,58,237,0.12);border:1px solid rgba(124,58,237,0.2);' : 'background:transparent;border:1px solid transparent;' }}
            {{ $hasUnread && !$isActive ? 'background:rgba(255,255,255,0.02);' : '' }}"
            onmouseover="this.style.background='rgba(255,255,255,0.04)'"
            onmouseout="this.style.background='{{ $isActive ? 'rgba(124,58,237,0.12)' : ($hasUnread ? 'rgba(255,255,255,0.02)' : 'transparent') }}'">
            
            @if($hasUnread && !$isActive)
                <div style="position:absolute;top:14px;right:14px;width:10px;height:10px;background:var(--accent);border-radius:50%;box-shadow:0 0 12px var(--accent);"></div>
            @endif

            <div style="position:relative;flex-shrink:0;">
                <div style="width:52px;height:52px;border-radius:16px;overflow:hidden;border:2px solid {{ $hasUnread ? 'var(--accent)' : ($isActive ? 'var(--accent-light)' : 'var(--border)') }}; transition: all 0.3s;">
                    <img src="{{ $otherUser->avatar ? asset('storage/'.$otherUser->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$otherUser->name }}" style="width:100%;height:100%;object-fit:cover;">
                </div>
                <div style="position:absolute;bottom:-2px;right:-2px;width:14px;height:14px;background:var(--green);border:3px solid var(--bg);border-radius:50%;"></div>
            </div>

            <div style="flex:1;min-width:0;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                    <p style="font-size:14px;font-weight:{{ $hasUnread ? '900' : '700' }};color:{{ $hasUnread ? 'var(--text)' : 'var(--text)' }};margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;letter-spacing:-0.01em;">{{ $otherUser->display_name ?? $otherUser->name }}</p>
                    @if($latestMsg)
                        <span style="font-size:10px;color:{{ $hasUnread ? 'var(--accent-light)' : 'var(--text-3)' }};font-weight:700;">{{ $latestMsg->sent_at->format('H:i') }}</span>
                    @endif
                </div>
                <p style="font-size:12px;color:{{ $hasUnread ? 'var(--text)' : 'var(--text-2)' }};font-weight:{{ $hasUnread ? '600' : '400' }};margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ $latestMsg?->content ?? 'Start a conversation' }}
                </p>
            </div>
        </a>
    @empty
        <div style="padding:60px 20px;text-align:center;color:var(--text-3);border:2px dashed var(--border);border-radius:20px;background:rgba(255,255,255,0.01);">
            <p style="font-size:13px;font-weight:600;">No active chats</p>
            <p style="font-size:11px;margin-top:4px;">Book an E-Buddy to start chatting.</p>
        </div>
    @endforelse
</div>
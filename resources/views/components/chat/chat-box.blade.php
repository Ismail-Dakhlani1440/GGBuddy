<?php

use Livewire\Component;
use App\Models\ChatRoom;
use App\Models\Message;
use App\Events\MessageSent;
use App\Events\MessageRead;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    public $roomId;
    public $messageText = '';
    public $inputKey = 0;

    public function mount($roomId)
    {
        $this->roomId = $roomId;
        $this->markAsRead();
    }

    public function markAsRead()
    {
        $updated = Message::where('chat_room_id', $this->roomId)
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        if ($updated) {
            broadcast(new \App\Events\MessageRead($this->roomId, auth()->id()))->toOthers();
        }
        
        $this->dispatch('messagesRead');
    }

    public function getListeners()
    {
        return [
            "echo-private:chat.room.{$this->roomId},MessageSent" => 'refreshMessages',
            "echo-private:chat.room.{$this->roomId},MessageRead" => '$refresh',
        ];
    }

    public function refreshMessages()
    {
        $this->markAsRead();
    }

    public function sendMessage()
    {
        $content = trim($this->messageText);
        if (!$content) return;

        try {
            $message = Message::create([
                'chat_room_id' => $this->roomId,
                'sender_id' => Auth::id(),
                'content' => $content,
                'sent_at' => now(),
            ]);

            broadcast(new MessageSent($message))->toOthers();

            $this->messageText = '';
            $this->inputKey++; // Increment key to force DOM reset
            
            $this->dispatch('message-sent');
        } catch (\Exception $e) {
            Log::error('Chat sendMessage failed: ' . $e->getMessage());
        }
    }

    public function getMessagesProperty()
    {
        return Message::where('chat_room_id', $this->roomId)
            ->with('sender')
            ->orderBy('sent_at', 'asc')
            ->get();
    }

    public function getRoomProperty()
    {
        return ChatRoom::with(['player', 'eBuddy'])->find($this->roomId);
    }
};
?>

<div class="chat-container" style="display:flex;flex-direction:column;height:650px;background:var(--surface);border-radius:24px;border:1px solid var(--border);overflow:hidden;">
    
    {{-- Chat Header --}}
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;background:rgba(255,255,255,0.02);">
        @php $otherUser = $this->room->player_id === auth()->id() ? $this->room->eBuddy : $this->room->player; @endphp
        
        <a href="{{ route('browse.show', $otherUser) }}" style="display:flex;align-items:center;gap:12px;text-decoration:none;transition:opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
            <div style="width:44px;height:44px;border-radius:14px;overflow:hidden;border:2px solid {{ $otherUser->isEBuddy() ? 'var(--accent-light)' : 'var(--border)' }};">
                <img src="{{ $otherUser->avatar ? asset('storage/'.$otherUser->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$otherUser->name }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div>
                <p style="font-size:15px;font-weight:800;color:var(--text);margin:0;">{{ $otherUser->display_name ?? $otherUser->name }}</p>
                @if($otherUser->isEBuddy())
                    <p style="font-size:11px;color:var(--green);display:flex;align-items:center;gap:4px;margin:0;">
                        <span style="width:6px;height:6px;background:var(--green);border-radius:50%;box-shadow:0 0 8px var(--green);"></span> Online
                    </p>
                @else
                    <p style="font-size:11px;color:var(--text-3);margin:0;">Player</p>
                @endif
            </div>
        </a>

        <a href="{{ route('browse.show', $otherUser) }}" class="btn btn-ghost btn-sm" style="border-radius:12px;">View Profile</a>
    </div>

    {{-- Messages Area --}}
    <div id="chat-window" 
         x-data
         x-init="$el.scrollTop = $el.scrollHeight" 
         x-on:message-sent.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })"
         style="flex:1;overflow-y:auto;padding:24px;display:flex;flex-direction:column;gap:20px;background:linear-gradient(to bottom, transparent, rgba(124,58,237,0.03)); scroll-behavior: smooth;">
        
        @foreach($this->messages as $msg)
            <div style="display:flex;flex-direction:column;align-items:{{ $msg->sender_id === auth()->id() ? 'flex-end' : 'flex-start' }};">
                <div style="max-width:70%;padding:14px 18px;border-radius:20px;font-size:14px;line-height:1.6;
                    {{ $msg->sender_id === auth()->id() 
                        ? 'background:linear-gradient(135deg, var(--accent), #9333ea);color:white;border-bottom-right-radius:4px;box-shadow: 0 4px 15px rgba(124,58,237,0.2);' 
                        : 'background:var(--surface2);color:var(--text);border-bottom-left-radius:4px;border:1px solid var(--border);' }}">
                    {{ $msg->content }}
                </div>
                
                <div style="display:flex;align-items:center;gap:6px;margin-top:6px;padding:0 4px;">
                    <span style="font-size:10px;color:var(--text-3);font-weight:600;">
                        {{ $msg->sent_at->format('H:i') }}
                    </span>
                    @if($msg->sender_id === auth()->id())
                        <span style="font-size:12px;color:{{ $msg->read_at ? 'var(--accent-light)' : 'var(--text-3)' }};">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M20 6L9 17L4 12"></path>
                                @if($msg->read_at)
                                    <path d="M16 6L9 13.5L7 11.5" opacity="0.7"></path>
                                @endif
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Input Area --}}
    <div style="padding:24px;background:rgba(255,255,255,0.02);border-top:1px solid var(--border);">
        <form wire:submit.prevent="sendMessage" style="display:flex;gap:12px;align-items:center;">
            <div style="flex:1;position:relative;">
                <input type="text" 
                       wire:model.live="messageText" 
                       wire:key="input-key-{{ $inputKey }}"
                       placeholder="Message..." 
                       autocomplete="off"
                       style="width:100%;background:var(--bg);border:1px solid var(--border);border-radius:16px;padding:14px 20px;color:var(--text);font-size:14px;outline:none;transition:all 0.2s;"
                       onfocus="this.style.borderColor='var(--accent)'; this.style.boxShadow='0 0 0 3px rgba(124,58,237,0.1)'" 
                       onblur="this.style.borderColor='var(--border)'; this.style.boxShadow='none'">
            </div>
            <button type="submit" 
                    class="btn btn-primary" style="border-radius:16px;width:48px;height:48px;padding:0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"></path></svg>
            </button>
        </form>
    </div>
</div>
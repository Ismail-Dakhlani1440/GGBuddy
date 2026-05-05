<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        $room = $this->message->chatRoom()->first();
        if (!$room) return [new PrivateChannel('chat.room.' . $this->message->chat_room_id)];

        $recipientId = ($this->message->sender_id === $room->player_id) 
            ? $room->e_buddy_id 
            : $room->player_id;

        return [
            new PrivateChannel('chat.room.' . $this->message->chat_room_id),
            new PrivateChannel('App.Models.User.' . $recipientId),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message->load('sender'),
        ];
    }
}

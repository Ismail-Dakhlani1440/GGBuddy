<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index($roomId = null)
    {
        $room = null;
        if ($roomId) {
            $room = ChatRoom::findOrFail($roomId);
            $room->authorizeParticipant(auth()->user());
        }

        return view('chat.index', [
            'activeRoom' => $room,
        ]);
    }

    public function start($userId)
    {
        $playerId = auth()->id();
        $eBuddyId = $userId;

        // Ensure we don't chat with ourselves
        if ($playerId == $eBuddyId) {
            return redirect()->back()->with('error', 'You cannot chat with yourself.');
        }

        $room = ChatRoom::where(function($q) use ($playerId, $eBuddyId) {
            $q->where('player_id', $playerId)->where('e_buddy_id', $eBuddyId);
        })->orWhere(function($q) use ($playerId, $eBuddyId) {
            $q->where('player_id', $eBuddyId)->where('e_buddy_id', $playerId);
        })->first();

        if (!$room) {
            $room = ChatRoom::create([
                'player_id' => $playerId,
                'e_buddy_id' => $eBuddyId,
            ]);
        }

        return redirect()->route('chat', $room->id);
    }
}

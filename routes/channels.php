<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.room.{roomId}', function ($user, $roomId) {
    $room = \App\Models\ChatRoom::find($roomId);
    if (!$room) return false;
    return $room->hasParticipant($user->id);
});

Broadcast::channel('match.{matchId}', function ($user, $matchId) {
    $match = \App\Models\Matches::find($matchId);
    if (!$match) return false;
    
    if ($match->hasParticipant($user->id)) {
        return [
            'id' => $user->id,
            'name' => $user->display_name ?? $user->name,
            'avatar' => $user->avatar ? asset('storage/'.$user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed='.$user->name,
        ];
    }
    return false;
});

Broadcast::channel('matchmaking.user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
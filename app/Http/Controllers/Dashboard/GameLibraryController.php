<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreGameRequest;
use App\Models\PlayerGameProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GameLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addGame()
    {
        $user = request()->user();
        $games = \App\Models\Game::with('ranks')->get();
        
        return view('dashboards.profile.add-game', [
            'games' => $games,
            'existingGameIds' => $user->gameProfiles->pluck('game_id')->toArray(),
        ]);
    }

    public function storeGame(StoreGameRequest $request)
    {
        $user = $request->user();
        
        PlayerGameProfile::updateOrCreate(
            ['user_id' => $user->id, 'game_id' => $request->game_id],
            ['current_rank_id' => $request->rank_id, 'peak_rank_id' => $request->rank_id]
        );

        return redirect()->route('profile')->with('success', 'Game added to library!');
    }

    public function removeGame(PlayerGameProfile $profile)
    {
        Gate::authorize('delete', $profile);
        $user = request()->user();

        // Check if there are services for this game
        if ($user->isEBuddy() && $user->eBuddy) {
            $hasServices = $user->eBuddy->services()->where('game_id', $profile->game_id)->exists();
            if ($hasServices) {
                return back()->with('error', 'Cannot remove this game because you have active services for it. Please delete your services for this game first.');
            }
        }

        $profile->delete();

        return back()->with('success', 'Game removed from library.');
    }
}

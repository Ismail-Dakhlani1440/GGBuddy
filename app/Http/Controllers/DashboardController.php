<?php

namespace App\Http\Controllers;

use App\Models\PlayerGameProfile;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = request()->user();

        // 1. Check for suspension
        if ($user->is_suspended) {
            return redirect()->route('suspended');
        }

        // 2. Handle E-Buddy status
        if ($user->isEBuddy()) {
            if ($user->eBuddy && $user->eBuddy->isPending()) {
                return redirect()->route('ebuddy.pending');
            }
            return redirect()->route('ebuddy.dashboard');
        }

        // 3. Handle Admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        // 4. Default to Player Dashboard
        return redirect()->route('player.dashboard');
    }

    public function ebuddyDashboard()
    {
        $user = request()->user();
        if (!$user->isEBuddy()) abort(403);
        $ebuddy = $user->eBuddy;
        
        $totalOrders = $ebuddy->orders()->count();
        $pendingOrders = $ebuddy->orders()->where('status', 'pending')->count();
        $totalEarnings = $ebuddy->orders()->where('status', 'paid')->sum('total_amount');
        
        return view('dashboards.ebuddy', compact('ebuddy', 'totalOrders', 'pendingOrders', 'totalEarnings'));
    }

    public function showProfile()
    {
        $user = request()->user();
        $userProfiles = $user->gameProfiles()->with(['game', 'currentRank'])->get();

        return view('dashboards.profile.view', [
            'user' => $user,
            'ebuddy' => $user->eBuddy,
            'userProfiles' => $userProfiles,
        ]);
    }

    public function editProfile()
    {
        $user = request()->user();

        return view('dashboards.profile.edit', [
            'user' => $user,
            'ebuddy' => $user->eBuddy,
        ]);
    }

    public function updateProfile()
    {
        $user = request()->user();
        $ebuddy = $user->eBuddy;

        $validated = request()->validate([
            'display_name' => 'required|string|max:255',
            'timezone' => 'required|string|max:100',
            'avatar' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:1000',
        ]);

        // Handle Avatar Upload
        if (request()->hasFile('avatar')) {
            $path = request()->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->update([
            'display_name' => $validated['display_name'],
            'timezone' => $validated['timezone'],
        ]);

        if ($ebuddy) {
            $ebuddy->update([
                'bio' => $validated['bio'],
            ]);
        }

        return redirect()->route('ebuddy.profile')->with('success', 'Profile updated successfully!');
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

    public function storeGame()
    {
        $user = request()->user();
        
        $validated = request()->validate([
            'game_id' => 'required|exists:games,id',
            'rank_id' => 'required|exists:ranks,id',
        ]);

        \App\Models\PlayerGameProfile::updateOrCreate(
            ['user_id' => $user->id, 'game_id' => $validated['game_id']],
            ['current_rank_id' => $validated['rank_id'], 'peak_rank_id' => $validated['rank_id']]
        );

        return redirect()->route('ebuddy.profile')->with('success', 'Game added to library!');
    }

    public function removeGame(PlayerGameProfile $profile)
    {
        Gate::authorize('delete', $profile);

        $profile->delete();

        return back()->with('success', 'Game removed from library.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Order;

class BrowseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List all active E-Buddies with their services.
     */
    public function index()
    {
        $ebuddies = User::whereHas('role', fn($q) => $q->where('title', 'ebuddy'))
            ->where('id', '!=', auth()->id())
            ->whereHas('eBuddy', fn($q) => $q->where('status', 'active'))
            ->whereHas('eBuddy.services')
            ->with(['eBuddy.services.game', 'gameProfiles.game', 'gameProfiles.currentRank'])
            ->get();

        $layout = $this->resolveLayout();

        return view('browse.index', compact('ebuddies', 'layout'));
    }

    /**
     * Show a single E-Buddy's public profile with their services.
     */
    public function show(User $ebuddy)
    {
        $ebuddy->load(['eBuddy.services.game', 'eBuddy.reviews.player', 'gameProfiles.game', 'gameProfiles.currentRank']);
        $user = $ebuddy; 

        $layout = $this->resolveLayout();

        return view('browse.show', compact('user', 'ebuddy', 'layout'));
    }

    /**
     * Returns which layout to use based on the auth user's role.
     */
    private function resolveLayout(): string
    {
        return 'layouts.dashboard';
    }
}

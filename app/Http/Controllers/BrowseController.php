<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\EBuddyOrder;

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
        $ebuddies = User::whereHas('role', fn($q) => $q->where('name', 'ebuddy'))
            ->whereHas('eBuddy', fn($q) => $q->where('status', 'active'))
            ->with(['eBuddy', 'gameProfiles.game', 'gameProfiles.currentRank', 'services.game'])
            ->get();

        $layout = $this->resolveLayout();

        return view('browse.index', compact('ebuddies', 'layout'));
    }

    /**
     * Show a single E-Buddy's public profile with their services.
     */
    public function show(User $ebuddy)
    {
        // Make sure we're viewing an actual active E-Buddy
        abort_unless(
            $ebuddy->isEBuddy() && $ebuddy->eBuddy?->status === 'active',
            404
        );

        $ebuddy->load(['eBuddy', 'gameProfiles.game', 'gameProfiles.currentRank', 'services.game', 'services.rank']);

        $layout = $this->resolveLayout();

        return view('browse.show', compact('ebuddy', 'layout'));
    }

    /**
     * Place an order for a specific service.
     */
    public function order(Service $service)
    {
        $validated = request()->validate([
            'hours'   => 'required|numeric|min:1|max:24',
            'message' => 'nullable|string|max:500',
        ]);

        EBuddyOrder::create([
            'player_id'   => auth()->id(),
            'ebuddy_id'   => $service->user_id,
            'service_id'  => $service->id,
            'hours'       => $validated['hours'],
            'total_price' => $service->price * $validated['hours'],
            'message'     => $validated['message'] ?? null,
            'status'      => 'pending',
        ]);

        return redirect()->back()->with('success', 'Order placed! The E-Buddy will confirm shortly.');
    }

    /**
     * List orders placed BY the current user (player side).
     */
    public function myOrders()
    {
        $orders = EBuddyOrder::where('player_id', auth()->id())
            ->with(['service.game', 'eBuddy'])
            ->latest()
            ->get();

        $layout = $this->resolveLayout();

        return view('browse.my-orders', compact('orders', 'layout'));
    }

    /**
     * Returns which layout to use based on the auth user's role.
     */
    private function resolveLayout(): string
    {
        if (auth()->user()->isEBuddy()) {
            return 'layouts.dashboard';
        }
        return 'layouts.player';
    }
}

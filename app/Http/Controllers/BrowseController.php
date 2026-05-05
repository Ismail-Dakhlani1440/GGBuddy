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
        // Make sure we're viewing an actual active E-Buddy
        abort_unless(
            $ebuddy->isEBuddy() && $ebuddy->eBuddy?->status === 'active',
            404
        );

        $ebuddy->load(['eBuddy.services.game', 'gameProfiles.game', 'gameProfiles.currentRank']);

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

        Order::create([
            'player_id'    => auth()->id(),
            'e_buddy_id'   => $service->e_buddy_id,
            'service_id'   => $service->id,
            'total_amount' => $service->price * $validated['hours'],
            'status'       => 'pending',
            'expires_at'   => now()->addHours(24),
        ]);

        return redirect()->back()->with('success', 'Order placed! The E-Buddy will confirm shortly.');
    }

    /**
     * List orders placed BY the current user (player side).
     */
    public function myOrders()
    {
        $orders = Order::where('player_id', auth()->id())
            ->with(['service.game', 'eBuddy.user'])
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
        // Unifying layouts: layouts.dashboard uses policies to show correct header links
        return 'layouts.dashboard';
    }
}

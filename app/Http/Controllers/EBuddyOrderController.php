<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Gate;

class EBuddyOrderController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Order::class);
        $user = request()->user();
        
        $orders = Order::where('e_buddy_id', $user->id)
            ->with(['player', 'service.game'])
            ->latest()
            ->get();

        return view('dashboards.orders', compact('orders'));
    }

    public function accept(Order $order)
    {
        Gate::authorize('update', $order);

        if (!$order->isPending()) {
            return back()->with('error', 'Only pending orders can be accepted.');
        }

        $order->confirm();

        return back()->with('success', 'Order accepted successfully!');
    }

    public function refuse(Order $order)
    {
        Gate::authorize('update', $order);

        if (!$order->isPending()) {
            return back()->with('error', 'Only pending orders can be refused.');
        }

        $validated = request()->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $order->refuse($validated['reason'] ?? 'No reason provided.');

        return back()->with('success', 'Order refused.');
    }
}

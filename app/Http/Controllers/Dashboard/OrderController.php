<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $type = $request->query('type', $user->isEBuddy() ? 'incoming' : 'outgoing');
        $status = $request->query('status', 'all');

        // Authorization
        if ($type === 'incoming') {
            Gate::authorize('viewIncoming', Order::class);
        } else {
            Gate::authorize('viewOutgoing', Order::class);
        }

        $query = ($type === 'incoming') 
            ? Order::where('e_buddy_id', $user->id)
            : Order::where('player_id', $user->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->with(['player', 'eBuddy.user', 'service.game', 'payment'])
            ->latest()
            ->get();

        return view('dashboards.orders', [
            'orders' => $orders,
            'type' => $type,
            'status' => $status,
            'user' => $user
        ]);
    }

    public function accept(Order $order)
    {
        Gate::authorize('update', $order);

        if (!$order->isPending()) {
            return back()->with('error', 'Only pending orders can be accepted.');
        }

        $order->confirm();

        return back()->with('success', 'Order accepted! Waiting for player payment.');
    }

    public function refuse(Order $order, Request $request)
    {
        Gate::authorize('update', $order);

        if (!$order->isPending()) {
            return back()->with('error', 'Only pending orders can be refused.');
        }

        $validated = $request->validate([
            'refuse_reason' => 'required|string|min:5|max:255',
        ]);

        $order->refuse($validated['refuse_reason']);

        return back()->with('success', 'Order refused.');
    }

    public function pay(Order $order)
    {
        if (auth()->id() !== $order->player_id) {
            abort(403);
        }

        if (!$order->isConfirmed()) {
            return back()->with('error', 'Only confirmed orders can be paid.');
        }

        $order->pay();

        return back()->with('success', 'Payment successful! Your session is now active.');
    }

    public function cancel(Order $order)
    {
        if (auth()->id() !== $order->player_id) {
            abort(403);
        }

        if (!$order->isConfirmed() && !$order->isPending()) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        $order->cancel();

        return back()->with('success', 'Order cancelled.');
    }

    public function complete(Order $order)
    {
        Gate::authorize('update', $order);

        if (!$order->isPaid()) {
            return back()->with('error', 'Only paid orders can be marked as completed.');
        }

        if (!$order->hasSessionEnded()) {
            $remaining = now()->diffForHumans($order->paid_at->addHours($order->hours), true);
            return back()->with('error', "The session duration hasn't ended yet. Please wait another {$remaining}.");
        }

        $order->complete();

        return back()->with('success', 'Session marked as completed! Thank you.');
    }
}

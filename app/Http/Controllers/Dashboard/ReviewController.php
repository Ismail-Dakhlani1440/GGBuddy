<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreReviewRequest;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    /**
     * Store a new review for an order.
     */
    public function store(StoreReviewRequest $request, Order $order)
    {
        // Only the player of the order can leave a review
        if (auth()->id() !== $order->player_id) {
            abort(403);
        }

        if (!$order->isCompleted()) {
            return back()->with('error', 'You can only review completed sessions.');
        }

        if ($order->isReviewed()) {
            return back()->with('error', 'You have already reviewed this session.');
        }

        Review::create([
            'order_id'   => $order->id,
            'player_id'  => auth()->id(),
            'e_buddy_id' => $order->e_buddy_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        // Update E-Buddy global rating
        $order->eBuddy?->refreshGlobalRating();

        return back()->with('success', 'Thank you for your feedback! ⭐');
    }
}

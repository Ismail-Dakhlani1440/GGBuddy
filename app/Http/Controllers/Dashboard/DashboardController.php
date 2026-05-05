<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}

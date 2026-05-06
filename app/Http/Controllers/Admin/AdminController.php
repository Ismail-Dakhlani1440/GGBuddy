<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EBuddy;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the main admin dashboard with statistics.
     */
    public function dashboard()
    {
        $pendingEbuddiesCount = EBuddy::where('status', 'pending')->count();
        $reportsCount = Report::where('status', 'pending')->count();
        $usersCount = User::count();
        
        return view('dashboards.admin.overview', compact('pendingEbuddiesCount', 'reportsCount', 'usersCount'));
    }

    /**
     * Display a listing of all users.
     */
    public function indexUsers()
    {
        $users = User::with('role')->where('id', '!=', auth()->id())->latest()->paginate(20);
        
        return view('dashboards.admin.users.index', compact('users'));
    }

    /**
     * Display a listing of user reports.
     */
    public function indexReports()
    {
        $reports = Report::with(['reporter', 'target'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);
        
        return view('dashboards.admin.reports.index', compact('reports'));
    }

    /**
     * Show details of a specific report.
     */
    public function showReport(Report $report)
    {
        $report->load(['reporter', 'target']);
        
        return view('dashboards.admin.reports.show', compact('report'));
    }

    /**
     * Dismiss a report without taking action.
     */
    public function dismissReport(Report $report)
    {
        $report->status = 'resolved';
        $report->resolved_at = now();
        $report->save();

        return redirect()->route('admin.reports.index')->with('success', 'Report has been dismissed.');
    }

    /**
     * Suspend or unsuspend a user.
     */
    public function toggleSuspension(User $user)
    {
        // Prevent admins from suspending themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend yourself.');
        }

        $user->is_suspended = !$user->is_suspended;
        $user->save();

        $status = $user->is_suspended ? 'suspended' : 'unsuspended';
        return back()->with('success', "User has been {$status} successfully.");
    }

    /**
     * List pending E-Buddy applications.
     */
    public function ebuddyApplications()
    {
        $applications = EBuddy::with('user')
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);
            
        return view('dashboards.admin.ebuddies.index', compact('applications'));
    }

    /**
     * Approve an E-Buddy application.
     */
    public function approveEBuddy(EBuddy $ebuddy)
    {
        $ebuddy->status = 'active';
        $ebuddy->save();

        return redirect()->route('admin.ebuddies.index')->with('success', "E-Buddy application for {$ebuddy->user->name} approved!");
    }

    /**
     * Reject an E-Buddy application.
     */
    public function rejectEBuddy(EBuddy $ebuddy)
    {
        $ebuddy->status = 'rejected';
        $ebuddy->save();

        return redirect()->route('admin.ebuddies.index')->with('success', "E-Buddy application for {$ebuddy->user->name} rejected.");
    }
}

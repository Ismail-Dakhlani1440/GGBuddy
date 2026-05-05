<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'target_id' => 'required|exists:users,id',
            'reason'    => 'required|string|min:5|max:1000',
        ]);

        $report = new Report();
        $report->reporter_id = auth()->id();
        $report->target_id = $validated['target_id'];
        $report->reason = $validated['reason'];
        $report->status = 'pending';
        $report->save();

        return back()->with('success', 'User reported successfully. Admin will review it.');
    }
}

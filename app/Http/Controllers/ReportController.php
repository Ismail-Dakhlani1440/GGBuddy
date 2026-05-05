<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Report;
use App\Http\Requests\StoreReportRequest;
use Illuminate\Support\Facades\Gate;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request)
    {
        $target = User::findOrFail($request->target_id);

        Gate::authorize('create', [Report::class, $target]);

        Report::create([
            'reporter_id' => auth()->id(),
            'target_id'   => $target->id,
            'reason'      => $request->reason,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'User reported. Our team will investigate.');
    }
}

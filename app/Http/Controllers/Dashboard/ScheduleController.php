<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ScheduleRequest;
use App\Http\Requests\Dashboard\UnavailabilityRequest;
use App\Models\Schedual;
use App\Models\Unavailability;
use Illuminate\Support\Facades\Gate;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        Gate::authorize('viewAny', Schedual::class);
        $ebuddy = request()->user()->eBuddy;

        $schedules = Schedual::where('e_buddy_id', $ebuddy->user_id)
            ->orderByRaw("FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->orderBy('start_time')
            ->get();

        $unavailabilities = Unavailability::where('e_buddy_id', $ebuddy->user_id)
            ->where('end_datetime', '>=', now())
            ->orderBy('start_datetime')
            ->get();

        return view('dashboards.schedule', compact('schedules', 'unavailabilities'));
    }

    public function storeSchedule(ScheduleRequest $request)
    {
        Gate::authorize('create', Schedual::class);
        $ebuddy = $request->user()->eBuddy;

        Schedual::create([
            'e_buddy_id' => $ebuddy->user_id,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return back()->with('success', 'Schedule slot added!');
    }

    public function destroySchedule(Schedual $schedual)
    {
        Gate::authorize('delete', $schedual);

        $schedual->delete();

        return back()->with('success', 'Schedule slot removed!');
    }

    public function storeUnavailability(UnavailabilityRequest $request)
    {
        Gate::authorize('create', Unavailability::class);
        $ebuddy = $request->user()->eBuddy;

        Unavailability::create([
            'e_buddy_id' => $ebuddy->user_id,
            'start_datetime' => $request->start_datetime,
            'end_datetime' => $request->end_datetime,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Unavailability block added!');
    }

    public function destroyUnavailability(Unavailability $unavailability)
    {
        Gate::authorize('delete', $unavailability);

        $unavailability->delete();

        return back()->with('success', 'Unavailability block removed!');
    }
}

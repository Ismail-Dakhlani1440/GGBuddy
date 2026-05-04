<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceRequest;
use App\Models\Game;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;


class ServiceController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Service::class);
        $user = request()->user();
        $ebuddy = $user->eBuddy;
        
        // Only allow games that are in the user's library
        $games = Game::whereIn('id', $user->gameProfiles->pluck('game_id'))
            ->with('ranks')
            ->get();
            
        $services = Service::where('e_buddy_id', $ebuddy->user_id)->with(['game'])->get();

        return view('dashboards.services', compact('games', 'services'));
    }

    public function store(ServiceRequest $request)
    {
        Gate::authorize('create', Service::class);
        $ebuddy = $request->user()->eBuddy;

        Service::create([
            'e_buddy_id' => $ebuddy->user_id,
            'game_id' => $request->game_id,
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return back()->with('success', 'Service added successfully!');
    }

    public function destroy(Service $service)
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return back()->with('success', 'Service removed successfully!');
    }
}

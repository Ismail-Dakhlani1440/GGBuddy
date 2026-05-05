<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GameManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is.admin']);
    }

    public function index()
    {
        $games = Game::withCount('ranks')->latest()->paginate(10);
        return view('dashboards.admin.games.index', compact('games'));
    }

    public function create()
    {
        return view('dashboards.admin.games.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'ranks' => 'nullable|array',
            'ranks.*.title' => 'required_with:ranks|string|max:255',
            'ranks.*.tier' => 'required_with:ranks|integer|min:1',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverPath = $request->file('cover')->store('games', 'public');
        }

        $game = Game::create([
            'title' => $request->title,
            'description' => $request->description,
            'cover' => $coverPath,
        ]);

        if ($request->has('ranks')) {
            foreach ($request->ranks as $rankData) {
                $game->ranks()->create($rankData);
            }
        }

        return redirect()->route('admin.games.index')->with('success', 'Game added successfully!');
    }

    public function edit(Game $game)
    {
        $game->load('ranks');
        return view('dashboards.admin.games.edit', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover' => 'nullable|image|max:2048',
            'ranks' => 'nullable|array',
            'ranks.*.id' => 'nullable|exists:ranks,id',
            'ranks.*.title' => 'required_with:ranks|string|max:255',
            'ranks.*.tier' => 'required_with:ranks|integer|min:1',
        ]);

        if ($request->hasFile('cover')) {
            if ($game->cover) {
                Storage::disk('public')->delete($game->cover);
            }
            $game->cover = $request->file('cover')->store('games', 'public');
        }

        $game->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // Handle ranks update/create
        if ($request->has('ranks')) {
            $existingRankIds = collect($request->ranks)->pluck('id')->filter()->toArray();
            
            // Optional: delete ranks that are not in the request
            // $game->ranks()->whereNotIn('id', $existingRankIds)->delete();

            foreach ($request->ranks as $rankData) {
                if (isset($rankData['id'])) {
                    Rank::where('id', $rankData['id'])->update([
                        'title' => $rankData['title'],
                        'tier' => $rankData['tier'],
                    ]);
                } else {
                    $game->ranks()->create($rankData);
                }
            }
        }

        return redirect()->route('admin.games.index')->with('success', 'Game updated successfully!');
    }

    public function destroy(Game $game)
    {
        $game->delete(); // Soft delete
        return back()->with('success', 'Game deleted successfully!');
    }

    public function deleteRank(Rank $rank)
    {
        $rank->delete();
        return back()->with('success', 'Rank removed!');
    }
}

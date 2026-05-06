<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGameRequest;
use App\Http\Requests\Admin\UpdateGameRequest;
use App\Models\Game;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $games = Game::withCount('ranks')->latest()->paginate(12);
        return view('dashboards.admin.games.index', compact('games'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboards.admin.games.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameRequest $request)
    {
        try {
            DB::beginTransaction();

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
                foreach ($request->ranks as $index => $rankData) {
                    $iconPath = null;
                    if ($request->hasFile("ranks.{$index}.icon")) {
                        $iconPath = $request->file("ranks.{$index}.icon")->store('ranks', 'public');
                    }

                    $game->ranks()->create([
                        'title' => $rankData['title'],
                        'tier' => $rankData['tier'],
                        'icon' => $iconPath,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.games.index')->with('success', "{$game->title} has been added to the catalog.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create game: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        $game->load('ranks');
        return view('dashboards.admin.games.edit', compact('game'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameRequest $request, Game $game)
    {
        try {
            DB::beginTransaction();

            $data = $request->only(['title', 'description']);

            if ($request->hasFile('cover')) {
                if ($game->cover) {
                    Storage::disk('public')->delete($game->cover);
                }
                $data['cover'] = $request->file('cover')->store('games', 'public');
            }

            $game->update($data);

            if ($request->has('ranks')) {
                foreach ($request->ranks as $index => $rankData) {
                    $rankUpdateData = [
                        'title' => $rankData['title'],
                        'tier' => $rankData['tier']
                    ];

                    if ($request->hasFile("ranks.{$index}.icon")) {
                        // Find existing rank if any
                        if (!empty($rankData['id'])) {
                            $oldRank = $game->ranks()->find($rankData['id']);
                            if ($oldRank && $oldRank->icon) {
                                Storage::disk('public')->delete($oldRank->icon);
                            }
                        }
                        $rankUpdateData['icon'] = $request->file("ranks.{$index}.icon")->store('ranks', 'public');
                    }

                    if (!empty($rankData['id'])) {
                        $game->ranks()->where('id', $rankData['id'])->update($rankUpdateData);
                    } else {
                        $game->ranks()->create($rankUpdateData);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.games.index')->with('success', 'Game configuration updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->route('admin.games.index')->with('success', 'Game archived successfully.');
    }

    /**
     * Remove a specific rank tier.
     */
    public function destroyRank(Rank $rank)
    {
        if ($rank->icon) {
            Storage::disk('public')->delete($rank->icon);
        }
        $rank->delete();
        return back()->with('success', 'Rank tier removed.');
    }
}

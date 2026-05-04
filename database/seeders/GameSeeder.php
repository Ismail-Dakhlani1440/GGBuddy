<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $games = [
            [
                'title' => 'League of Legends',
                'description' => 'MOBA strategy game',
                'ranks' => ['Iron', 'Bronze', 'Silver', 'Gold', 'Platinum', 'Emerald', 'Diamond', 'Master', 'Grandmaster', 'Challenger']
            ],
            [
                'title' => 'Valorant',
                'description' => 'Tactical character-based shooter',
                'ranks' => ['Iron', 'Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Ascendant', 'Immortal', 'Radiant']
            ],
            [
                'title' => 'Overwatch 2',
                'description' => 'Team-based action game',
                'ranks' => ['Bronze', 'Silver', 'Gold', 'Platinum', 'Diamond', 'Master', 'Grandmaster', 'Top 500']
            ]
        ];

        foreach ($games as $gameData) {
            $game = \App\Models\Game::create([
                'title' => $gameData['title'],
                'description' => $gameData['description'],
                'image' => 'placeholder.png',
            ]);

            foreach ($gameData['ranks'] as $index => $rankTitle) {
                \App\Models\Rank::create([
                    'game_id' => $game->id,
                    'title' => $rankTitle,
                    'tier' => $index + 1,
                ]);
            }
        }
    }
}

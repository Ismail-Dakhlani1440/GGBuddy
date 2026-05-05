<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Role;
use App\Models\User;
use App\Models\EBuddy;
use App\Models\Service;
use App\Models\PlayerGameProfile;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $ebuddyRole = Role::where('title', 'ebuddy')->first();
        $games = Game::with('ranks')->get();

        if ($games->isEmpty() || !$ebuddyRole) {
            $this->command->error('Please run DatabaseSeeder first (needs games and roles).');
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            $user = User::factory()->create([
                'role_id' => $ebuddyRole->id,
            ]);

            $ebuddy = EBuddy::create([
                'user_id' => $user->id,
                'status' => 'active',
                'global_rating' => rand(30, 50) / 10,
                'bio' => fake()->paragraph(2),
            ]);

            // Assign 1 to 3 random games to this E-Buddy
            $randomGames = $games->random(rand(1, 3));

            foreach ($randomGames as $game) {
                if ($game->ranks->isEmpty()) continue;
                
                $randomRank = $game->ranks->random();

                PlayerGameProfile::create([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'current_rank_id' => $randomRank->id,
                    'peak_rank_id' => $randomRank->id,
                ]);

                Service::create([
                    'e_buddy_id' => $user->id,
                    'game_id' => $game->id,
                    'title' => 'I will carry you in ' . $game->title,
                    'description' => fake()->paragraph(),
                    'price' => rand(5, 50),
                ]);
            }
        }
        
        $this->command->info('Generated 15 demo E-Buddies with profiles and services!');
    }
}

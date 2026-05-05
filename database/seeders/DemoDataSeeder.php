<?php

namespace Database\Seeders;

use App\Models\EBuddy;
use App\Models\Game;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $ebuddyUser = User::where('email', 'ebuddy@ebuddy.com')->first();
        $playerUser = User::where('email', 'player@player.com')->first();
        $ebuddy = EBuddy::where('user_id', $ebuddyUser->id)->first();
        $games = Game::all();

        if (!$ebuddy || !$playerUser || $games->isEmpty()) {
            return;
        }

        // 1. Create Services
        $services = [];
        foreach ($games->take(3) as $game) {
            $services[] = Service::create([
                'e_buddy_id' => $ebuddy->user_id,
                'game_id'    => $game->id,
                'title'      => "Expert Sessions for {$game->title}",
                'description' => "Pro gameplay and tips for {$game->title}.",
                'price'      => 25,
            ]);
        }

        // 2. Lifecycle Test Cases
        
        // CASE: READY TO COMPLETE (For Review System Testing)
        // Duration: 1h, Started: 61m ago
        $readyToComplete = Order::create([
            'player_id'    => $playerUser->id,
            'e_buddy_id'   => $ebuddy->user_id,
            'service_id'   => $services[0]->id,
            'status'       => 'paid',
            'total_amount' => 25,
            'hours'        => 1,
            'paid_at'      => Carbon::now()->subMinutes(61), 
            'expires_at'   => Carbon::now()->subHours(2),
        ]);
        $readyToComplete->confirm();

        // CASE: IN PROGRESS (Should be blocked from completion)
        $inProgress = Order::create([
            'player_id'    => $playerUser->id,
            'e_buddy_id'   => $ebuddy->user_id,
            'service_id'   => $services[1]->id,
            'status'       => 'paid',
            'total_amount' => 50,
            'hours'        => 2,
            'paid_at'      => Carbon::now()->subMinutes(30), 
            'expires_at'   => Carbon::now()->subHours(1),
        ]);
        $inProgress->confirm();

        // CASE: PENDING
        Order::create([
            'player_id'    => $playerUser->id,
            'e_buddy_id'   => $ebuddy->user_id,
            'service_id'   => $services[2]->id,
            'status'       => 'pending',
            'total_amount' => 25,
            'hours'        => 1,
            'expires_at'   => Carbon::now()->addHour(),
        ]);
    }
}

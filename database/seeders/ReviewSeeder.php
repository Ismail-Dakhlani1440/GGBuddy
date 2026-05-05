<?php

namespace Database\Seeders;

use App\Models\EBuddy;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $ebuddy = User::where('email', 'ebuddy@ebuddy.com')->first();
        if (!$ebuddy) return;

        $service = $ebuddy->eBuddy->services->first();
        if (!$service) return;

        // Create some players for reviews
        $players = [];
        for ($i = 1; $i <= 5; $i++) {
            $players[] = User::factory()->create([
                'name' => "Reviewer $i",
                'display_name' => "Reviewer $i",
                'email' => "reviewer$i@example.com",
                'password' => 'password',
                'role_id' => \App\Models\Role::where('title', 'player')->first()->id,
            ]);
        }

        $reviewsData = [
            ['rating' => 5, 'comment' => 'Amazing session! Helped me climb from Bronze to Gold in one night.'],
            ['rating' => 4, 'comment' => 'Very friendly and patient. High mechanical skill!'],
            ['rating' => 5, 'comment' => 'Best e-buddy ever. Super chill and knows everything about the meta.'],
            ['rating' => 3, 'comment' => 'Good player, but was 5 minutes late. Still worth it though.'],
            ['rating' => 5, 'comment' => 'Highly recommended! I learned so much about positioning.'],
            ['rating' => 2, 'comment' => 'Connection was a bit laggy on their end, hard to communicate.'],
            ['rating' => 4, 'comment' => 'Great vibes and great plays. Will book again!'],
        ];

        foreach ($reviewsData as $index => $data) {
            $player = $players[array_rand($players)];
            
            // Create a completed order
            $order = Order::create([
                'player_id' => $player->id,
                'e_buddy_id' => $ebuddy->id,
                'service_id' => $service->id,
                'total_amount' => $service->price,
                'hours' => 1,
                'status' => 'completed',
                'paid_at' => Carbon::now()->subDays(rand(1, 10)),
                'expires_at' => Carbon::now(),
            ]);

            // Create review
            Review::create([
                'order_id' => $order->id,
                'player_id' => $player->id,
                'e_buddy_id' => $ebuddy->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'created_at' => $order->paid_at->addHours(2),
            ]);
        }

        // Refresh the global rating
        $ebuddy->eBuddy->refreshGlobalRating();
    }
}

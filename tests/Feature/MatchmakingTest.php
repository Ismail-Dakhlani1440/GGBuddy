<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Matches;
use App\Models\MatchmakingQueue;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use App\Events\MatchUpdated;
use App\Events\MatchFound;
use App\Jobs\FinalizeMatchJob;
use Tests\TestCase;

class MatchmakingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $game;

    protected function setUp(): void
    {
        parent::setUp();
        
        $role = Role::create(['title' => 'player']);
        $this->user = User::factory()->create(['role_id' => $role->id]);
        $this->game = Game::create(['title' => 'Test Game', 'slug' => 'test-game']);
        
        // Add game to user's library
        $this->user->gameProfiles()->create([
            'game_id' => $this->game->id,
            'current_rank_id' => null,
            'peak_rank_id' => null
        ]);
    }

    public function test_user_can_join_matchmaking_queue()
    {
        $response = $this->actingAs($this->user)
            ->post(route('matchmaking.join'), [
                'game_id' => $this->game->id
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('matchmaking_queues', [
            'player_id' => $this->user->id,
            'game_id' => $this->game->id,
            'status' => 'searching'
        ]);
    }

    public function test_joining_creates_a_pending_match()
    {
        $this->actingAs($this->user)->post(route('matchmaking.join'), [
            'game_id' => $this->game->id
        ]);

        $this->assertDatabaseHas('matches', [
            'game_id' => $this->game->id,
            'status' => 'pending'
        ]);

        $match = Matches::first();
        $this->assertTrue($match->hasParticipant($this->user->id));
    }

    public function test_second_player_starts_timer_and_dispatches_job()
    {
        Queue::fake();
        Event::fake([MatchUpdated::class]);

        // Player 1
        $this->actingAs($this->user)->post(route('matchmaking.join'), [
            'game_id' => $this->game->id
        ]);

        // Player 2
        $user2 = User::factory()->create(['role_id' => $this->user->role_id]);
        $this->actingAs($user2)->post(route('matchmaking.join'), [
            'game_id' => $this->game->id
        ]);

        $match = Matches::first();
        $this->assertNotNull($match->finalizes_at);
        $this->assertEquals(2, $match->participations()->count());

        Queue::assertPushed(FinalizeMatchJob::class);
        Event::assertDispatched(MatchUpdated::class);
    }

    public function test_fifth_player_activates_match_immediately()
    {
        Event::fake([MatchFound::class]);

        // Join 4 players
        for ($i = 1; $i <= 4; $i++) {
            $u = User::factory()->create(['role_id' => $this->user->role_id]);
            $this->actingAs($u)->post(route('matchmaking.join'), [
                'game_id' => $this->game->id
            ]);
        }

        $match = Matches::first();
        $this->assertEquals('pending', $match->status);

        // Join 5th player
        $user5 = User::factory()->create(['role_id' => $this->user->role_id]);
        $this->actingAs($user5)->post(route('matchmaking.join'), [
            'game_id' => $this->game->id
        ]);

        $match->refresh();
        $this->assertEquals('active', $match->status);
        $this->assertNull($match->finalizes_at);

        Event::assertDispatched(MatchFound::class);
    }

    public function test_finalize_job_activates_match_after_timer()
    {
        Event::fake([MatchFound::class]);

        // Two players join
        $user2 = User::factory()->create(['role_id' => $this->user->role_id]);
        $this->actingAs($this->user)->post(route('matchmaking.join'), ['game_id' => $this->game->id]);
        $this->actingAs($user2)->post(route('matchmaking.join'), ['game_id' => $this->game->id]);

        $match = Matches::first();
        $this->assertEquals('pending', $match->status);

        // Manually trigger job after setting time to past
        $match->update(['finalizes_at' => now()->subMinute()]);
        
        $job = new FinalizeMatchJob($match);
        $job->handle();

        $match->refresh();
        $this->assertEquals('active', $match->status);
        Event::assertDispatched(MatchFound::class);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(GameSeeder::class);

        $adminRole = Role::where('title', 'admin')->first();
        $ebuddyRole = Role::where('title', 'ebuddy')->first();
        $playerRole = Role::where('title', 'player')->first();

        // 1. Admin
        User::factory()->create([
            'role_id' => $adminRole->id,
            'name' => 'System Admin',
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);

        // 2. Player
        User::factory()->create([
            'role_id' => $playerRole->id,
            'name' => 'Casual Player',
            'email' => 'player@player.com',
            'password' => 'password',
        ]);

        // 3. Active E-Buddy
        $activeEbuddy = User::factory()->create([
            'role_id' => $ebuddyRole->id,
            'name' => 'Active E-Buddy',
            'email' => 'ebuddy@ebuddy.com',
            'password' => 'password',
        ]);
        \App\Models\EBuddy::create([
            'user_id' => $activeEbuddy->id,
            'status' => 'active',
            'bio' => 'Professional gamer at your service.',
        ]);

        // 4. Pending E-Buddy
        $pendingEbuddy = User::factory()->create([
            'role_id' => $ebuddyRole->id,
            'name' => 'Pending E-Buddy',
            'email' => 'pending@ebuddy.com',
            'password' => 'password',
        ]);
        \App\Models\EBuddy::create([
            'user_id' => $pendingEbuddy->id,
            'status' => 'pending',
            'bio' => 'I hope I get approved soon!',
        ]);

        // 5. Suspended User
        User::factory()->create([
            'role_id' => $playerRole->id,
            'name' => 'Suspended User',
            'email' => 'suspended@user.com',
            'password' => 'password',
            'is_suspended' => true,
        ]);
    }
}

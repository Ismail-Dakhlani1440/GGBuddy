<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showProfile()
    {
        $user = request()->user();
        $userProfiles = $user->gameProfiles()->with(['game', 'currentRank'])->get();

        if ($user->isEBuddy()) {
            $user->load(['eBuddy.reviews.player']);
        }

        return view('dashboards.profile.view', [
            'user' => $user,
            'ebuddy' => $user->eBuddy,
            'userProfiles' => $userProfiles,
        ]);
    }

    public function publicProfile(\App\Models\User $user)
    {
        // If viewing own profile, redirect to the dashboard view
        if ($user->id === auth()->id()) {
            return redirect()->route('profile');
        }

        $user->load(['eBuddy.services.game', 'gameProfiles.game', 'gameProfiles.currentRank']);
        $userProfiles = $user->gameProfiles;

        return view('dashboards.profile.public', [
            'user' => $user,
            'ebuddy' => $user->eBuddy,
            'userProfiles' => $userProfiles,
        ]);
    }

    public function editProfile()
    {
        $user = request()->user();

        return view('dashboards.profile.edit', [
            'user' => $user,
            'ebuddy' => $user->eBuddy,
        ]);
    }

    public function updateProfile()
    {
        $user = request()->user();
        $ebuddy = $user->eBuddy;

        $validated = request()->validate([
            'display_name' => 'required|string|max:255',
            'timezone' => 'required|string|max:100',
            'avatar' => 'nullable|image|max:2048',
            'bio' => 'nullable|string|max:1000',
            'banner' => 'nullable|image|max:4096|dimensions:min_width=1200,min_height=400',
            'browser_notifications' => 'nullable|boolean',
            'sound_enabled' => 'nullable|boolean',
        ]);

        // Handle Avatar Upload
        if (request()->hasFile('avatar')) {
            $path = request()->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->update([
            'display_name' => $validated['display_name'] ?? $user->display_name,
            'timezone' => $validated['timezone'] ?? $user->timezone,
        ]);

        // Update Notification Settings
        $user->notificationSetting()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'browser_notifications' => request()->has('browser_notifications'),
                'sound_enabled' => request()->has('sound_enabled'),
            ]
        );

        if ($ebuddy) {
            // Handle Banner Upload
            if (request()->hasFile('banner') && request()->file('banner')->isValid()) {
                $path = request()->file('banner')->store('banners', 'public');
                $ebuddy->banner = $path;
            }

            if (array_key_exists('bio', $validated)) {
                $ebuddy->bio = $validated['bio'];
            }

            $ebuddy->save();
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
}

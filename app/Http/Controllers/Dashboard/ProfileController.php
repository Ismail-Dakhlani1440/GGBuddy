<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateProfileRequest;
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

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $ebuddy = $user->eBuddy;

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->update([
            'display_name' => $request->display_name ?? $user->display_name,
            'timezone' => $request->timezone ?? $user->timezone,
        ]);

        // Update Notification Settings
        $user->notificationSetting()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'browser_notifications' => $request->has('browser_notifications'),
                'sound_enabled' => $request->has('sound_enabled'),
            ]
        );

        if ($ebuddy) {
            // Handle Banner Upload
            if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                $path = $request->file('banner')->store('banners', 'public');
                $ebuddy->banner = $path;
            }

            if ($request->has('bio')) {
                $ebuddy->bio = $request->bio;
            }

            $ebuddy->save();
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
}

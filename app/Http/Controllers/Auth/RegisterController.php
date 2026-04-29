<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\EBuddy;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $roleTitle = $validated['role'];
        
        $role = Role::query()
            ->where('title', $roleTitle)
            ->first();

        if (! $role) {
            abort(500, "Role '{$roleTitle}' is missing. Run seeders.");
        }

        $user = User::create([
            'role_id' => $role->id,
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'avatar' => $validated['avatar'] ?? null,
            'timezone' => $validated['timezone'] ?? 'UTC',
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if ($roleTitle === 'ebuddy') {
            EBuddy::create([
                'user_id' => $user->id,
                'bio' => $validated['bio'] ?? null,
                'status' => 'pending',
                'global_rating' => 0.00,
                'missed_order_count' => 0,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}

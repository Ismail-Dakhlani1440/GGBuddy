<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = $validated['email'];
        $password = $validated['password'];

    
        $ok = Auth::attempt(
            [
                'email' => $email,
                'password' => $password,
            ],
            false
        );

        if (! $ok) {
            return back()
                ->withErrors([
                    'email' => 'These credentials do not match our records.',
                ])
                ->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}

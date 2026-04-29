@extends('layouts.ggbuddy')

@section('content')
    <main class="w-full max-w-md">
        <section class="rounded-xl border border-white/10 bg-white/[0.03] backdrop-blur px-6 py-8 shadow-[0_0_0_1px_rgba(139,92,246,0.12)]">
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-semibold leading-tight">Dashboard</h1>
                    <p class="text-sm text-white/70">Your mission control</p>
                </div>

                <div class="hidden sm:block text-right">
                    <div class="text-xs text-white/60">Session</div>
                    <div class="text-sm font-medium text-[#A855F7]">Auth</div>
                </div>
            </div>

            @if ($user)
                <div class="mb-6 rounded-lg border border-white/10 bg-black/15 p-4">
                    <div class="text-sm text-white/60">Signed in as</div>
                    <div class="text-lg font-semibold mt-1">
                        {{ $user->display_name ?? $user->name }}
                    </div>
                    <div class="text-xs text-white/60 mt-1 break-all">
                        {{ $user->email }}
                    </div>
                </div>
            @endif

            <div class="space-y-4">
                {{-- Placeholder game stats --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-white/10 bg-black/15 p-4">
                        <div class="text-xs text-white/60">Rank</div>
                        <div class="text-lg font-semibold mt-1">Trainee</div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-black/15 p-4">
                        <div class="text-xs text-white/60">Queue</div>
                        <div class="text-lg font-semibold mt-1">Ready</div>
                    </div>
                </div>

                <div class="rounded-lg border border-white/10 bg-black/15 p-4">
                    <div class="text-sm text-white/70 mb-2">Next objectives</div>
                    <ul class="text-sm space-y-2">
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8B5CF6] shadow-[0_0_18px_rgba(139,92,246,0.7)]"></span>
                            Complete your profile
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#A855F7] shadow-[0_0_18px_rgba(168,85,247,0.7)]"></span>
                            Join a match
                        </li>
                    </ul>
                </div>

                {{-- Logout --}}
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-md px-4 py-2.5
                                   bg-black/25 hover:bg-black/35
                                   border border-white/10
                                   shadow-[0_0_25px_rgba(139,92,246,0.10)]
                                   text-white font-medium transition">
                        <span>Log out</span>
                        <span aria-hidden="true">⟲</span>
                    </button>
                </form>
            </div>
        </section>
    </main>
@endsection

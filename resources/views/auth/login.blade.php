@extends('layouts.ggbuddy')

@section('content')
    <div class="w-full max-w-lg h-full pb-10">
        <section class="rounded-2xl border border-white/10 bg-white/[0.02] backdrop-blur-xl px-8 py-10 shadow-[0_0_40px_rgba(139,92,246,0.15)] relative overflow-hidden">
            
            {{-- Decorative glow --}}
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-[#8B5CF6]/30 rounded-full blur-3xl pointer-events-none"></div>
            
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 mb-8 relative z-10">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-white/70">Welcome Back</h1>
                    <p class="text-sm text-white/60 mt-1">Ready to enter the arena?</p>
                </div>
            </div>

            {{-- Status --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ url('/login') }}" class="space-y-5 relative z-10">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium mb-1.5 text-white/80">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50"
                           placeholder="you@example.com" autocomplete="email" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-white/80">Password</label>
                    </div>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50"
                           placeholder="••••••••" autocomplete="current-password" />
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="group w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3.5
                                   bg-gradient-to-r from-[#8B5CF6] to-[#A855F7] hover:from-[#7C3AED] hover:to-[#9333EA]
                                   shadow-[0_0_20px_rgba(139,92,246,0.3)] hover:shadow-[0_0_30px_rgba(139,92,246,0.5)]
                                   text-white font-semibold transition-all duration-200 transform hover:-translate-y-0.5">
                        <span>Log in to your account</span>
                        <span aria-hidden="true" class="transition-transform group-hover:translate-x-1">→</span>
                    </button>
                </div>

                <div class="pt-4 mt-6 border-t border-white/5 text-sm text-center">
                    <span class="text-white/50">Don't have an account?</span>
                    <a href="{{ url('/register') }}" class="text-[#C084FC] font-medium hover:text-white transition-colors ml-1">Create one now</a>
                </div>
            </form>
        </section>

    </div>
@endsection

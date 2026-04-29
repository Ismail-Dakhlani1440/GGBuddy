@extends('layouts.ggbuddy')

@section('content')
    <div class="w-full max-w-lg h-full pb-10" x-data="{ role: 'player' }">
        <section class="rounded-2xl border border-white/10 bg-white/[0.02] backdrop-blur-xl px-8 py-10 shadow-[0_0_40px_rgba(139,92,246,0.15)] relative overflow-hidden">
            
            {{-- Decorative glow --}}
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#8B5CF6]/30 rounded-full blur-3xl pointer-events-none"></div>
            
            {{-- Header --}}
            <div class="flex items-start justify-between gap-4 mb-8 relative z-10">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-white to-white/70">Create Account</h1>
                    <p class="text-sm text-white/60 mt-1">Join the ultimate gaming lounge</p>
                </div>
            </div>

            {{-- Status --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ url('/register') }}" class="space-y-5 relative z-10">
                @csrf

                {{-- Role Selection --}}
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-white/80">I want to join as a...</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex cursor-pointer rounded-xl border bg-black/20 p-4 shadow-sm hover:bg-black/30 transition-all"
                               :class="role === 'player' ? 'border-[#8B5CF6] shadow-[0_0_15px_rgba(139,92,246,0.2)]' : 'border-white/10'">
                            <input type="radio" name="role" value="player" class="sr-only" x-model="role" checked>
                            <div class="flex w-full items-center justify-center">
                                <div class="text-sm font-semibold" :class="role === 'player' ? 'text-[#C084FC]' : 'text-white/70'">Player</div>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-xl border bg-black/20 p-4 shadow-sm hover:bg-black/30 transition-all"
                               :class="role === 'ebuddy' ? 'border-[#8B5CF6] shadow-[0_0_15px_rgba(139,92,246,0.2)]' : 'border-white/10'">
                            <input type="radio" name="role" value="ebuddy" class="sr-only" x-model="role">
                            <div class="flex w-full items-center justify-center">
                                <div class="text-sm font-semibold" :class="role === 'ebuddy' ? 'text-[#C084FC]' : 'text-white/70'">E-Buddy</div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-1.5 text-white/80">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50" placeholder="John Doe" />
                        @error('name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="display_name" class="block text-sm font-medium mb-1.5 text-white/80">Gamer Tag</label>
                        <input id="display_name" name="display_name" type="text" value="{{ old('display_name') }}" required
                               class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50" placeholder="AwesomeGamer99" />
                        @error('display_name')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1.5 text-white/80">Email Address</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50"
                           placeholder="you@example.com" autocomplete="email" />
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-sm font-medium mb-1.5 text-white/80">Password</label>
                        <input id="password" name="password" type="password" required
                               class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50"
                               placeholder="••••••••" autocomplete="new-password" />
                        @error('password')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium mb-1.5 text-white/80">Confirm Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50"
                               placeholder="••••••••" autocomplete="new-password" />
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- E-Buddy Bio (Conditional) --}}
                <div x-show="role === 'ebuddy'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="pt-2" style="display: none;">
                    <label for="bio" class="block text-sm font-medium mb-1.5 text-white/80">Bio <span class="text-xs text-white/40 font-normal">(Optional)</span></label>
                    <textarea id="bio" name="bio" rows="3"
                              class="w-full rounded-xl border border-white/10 bg-black/40 px-4 py-2.5 text-white placeholder-white/30 outline-none transition-all focus:border-[#8B5CF6] focus:bg-black/60 focus:ring-1 focus:ring-[#8B5CF6]/50 resize-none"
                              placeholder="Tell players about your gaming style and skills..."></textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit"
                            class="group w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3.5
                                   bg-gradient-to-r from-[#8B5CF6] to-[#A855F7] hover:from-[#7C3AED] hover:to-[#9333EA]
                                   shadow-[0_0_20px_rgba(139,92,246,0.3)] hover:shadow-[0_0_30px_rgba(139,92,246,0.5)]
                                   text-white font-semibold transition-all duration-200 transform hover:-translate-y-0.5">
                        <span>Create Account</span>
                        <span aria-hidden="true" class="transition-transform group-hover:translate-x-1">→</span>
                    </button>
                </div>

                <div class="pt-4 mt-6 border-t border-white/5 text-sm text-center">
                    <span class="text-white/50">Already have an account?</span>
                    <a href="{{ url('/login') }}" class="text-[#C084FC] font-medium hover:text-white transition-colors ml-1">Log in</a>
                </div>
            </form>
        </section>
        
    </div>

    <!-- Alpine.js for role toggle -->
    <script src="//unpkg.com/alpinejs" defer></script>
@endsection

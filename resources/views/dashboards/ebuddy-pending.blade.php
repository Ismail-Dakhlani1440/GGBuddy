@extends('layouts.ggbuddy')

@section('content')
<div class="max-w-2xl w-full">
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-12 text-center shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-[#8B5CF6]/5 to-transparent pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-[#8B5CF6]/10 border border-[#8B5CF6]/20 mb-8 animate-pulse">
                <span class="text-4xl">⏳</span>
            </div>
            
            <h1 class="text-4xl font-bold tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/60">Application Pending</h1>
            <p class="text-white/70 text-lg mb-8 leading-relaxed">
                Thanks for joining the GGBuddy lounge! Your E-Buddy application is currently being reviewed by our moderation team.
            </p>
            
            <div class="p-6 rounded-2xl bg-black/40 border border-white/5 text-sm text-white/50 inline-block">
                We'll notify you via email as soon as your account is activated.
            </div>
            
            <div class="mt-12 pt-8 border-t border-white/5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[#C084FC] hover:text-white transition-colors font-medium">Log out and return later</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

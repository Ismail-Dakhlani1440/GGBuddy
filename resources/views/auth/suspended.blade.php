@extends('layouts.ggbuddy')

@section('content')
<div class="max-w-2xl w-full">
    <div class="bg-white/5 backdrop-blur-xl border border-red-500/20 rounded-3xl p-12 text-center shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-red-500/5 to-transparent pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-500/10 border border-red-500/20 mb-8 shadow-[0_0_30px_rgba(239,68,68,0.2)]">
                <span class="text-4xl">🚫</span>
            </div>
            
            <h1 class="text-4xl font-bold tracking-tight mb-4 text-red-500">Account Suspended</h1>
            <p class="text-white/70 text-lg mb-8 leading-relaxed">
                Your account has been suspended due to a violation of our terms of service or community guidelines.
            </p>
            
            <div class="p-6 rounded-2xl bg-black/40 border border-white/5 text-sm text-white/50 inline-block">
                If you believe this was a mistake, please contact support.
            </div>
            
            <div class="mt-12 pt-8 border-t border-white/5">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/40 hover:text-white transition-colors font-medium">Log out</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

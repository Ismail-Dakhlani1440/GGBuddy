@extends('layouts.ggbuddy')

@section('content')
<div class="max-w-2xl w-full">
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-12 text-center shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#8B5CF6]/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/5 border border-white/10 mb-8 shadow-inner">
                <span class="text-4xl">🎮</span>
            </div>
            
            <h1 class="text-4xl font-bold tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/60">Player Dashboard</h1>
            <p class="text-white/60 text-lg mb-8">Ready for your next session? Browse E-Buddies and level up.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#" class="px-8 py-3 rounded-xl bg-white text-black font-semibold hover:bg-white/90 transition-all">Find a Buddy</a>
                <a href="#" class="px-8 py-3 rounded-xl bg-white/5 border border-white/10 text-white font-semibold hover:bg-white/10 transition-all">My Orders</a>
            </div>
        </div>
    </div>
</div>
@endsection

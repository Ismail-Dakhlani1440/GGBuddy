@extends('layouts.ggbuddy')

@section('content')
<div class="max-w-2xl w-full">
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl p-12 text-center shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-[#A855F7]/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white/5 border border-white/10 mb-8 shadow-inner">
                <span class="text-4xl">🛡️</span>
            </div>
            
            <h1 class="text-4xl font-bold tracking-tight mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/60">Admin Dashboard</h1>
            <p class="text-white/60 text-lg mb-8">Welcome to the command center. Management tools coming soon.</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10">
                    <div class="text-2xl font-bold text-[#A855F7]">0</div>
                    <div class="text-sm text-white/40">Pending Buddies</div>
                </div>
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10">
                    <div class="text-2xl font-bold text-[#C084FC]">0</div>
                    <div class="text-sm text-white/40">Open Reports</div>
                </div>
                <div class="p-6 rounded-2xl bg-white/5 border border-white/10">
                    <div class="text-2xl font-bold text-white">0</div>
                    <div class="text-sm text-white/40">Total Users</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

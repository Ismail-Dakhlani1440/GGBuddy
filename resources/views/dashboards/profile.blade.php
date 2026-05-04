@extends('layouts.dashboard', ['title' => 'Profile Customization'])

@section('content')
<div class="max-w-5xl space-y-10" x-data="{ 
    selectedGames: {{ $userProfiles->keys()->toJson() }},
    games: {{ $games->toJson() }},
    getAvailableRanks(gameId) {
        const game = this.games.find(g => g.id == gameId);
        return game ? game.ranks : [];
    }
}">
    <div class="flex flex-col gap-2">
        <h1 class="text-4xl font-bold tracking-tight">Profile & Identity</h1>
        <p class="text-white/40 text-sm">Customize how players see you in the lounge.</p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('ebuddy.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- Left Column: Avatar & Core Info --}}
            <div class="lg:col-span-1 space-y-8">
                <div class="p-10 rounded-[3rem] glass-panel border-white/5 flex flex-col items-center text-center">
                    <div class="relative group cursor-pointer mb-6">
                        <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-[#8B5CF6]/20 group-hover:border-[#8B5CF6]/50 transition-all">
                            <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . $user->name }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        <label for="avatar" class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 rounded-full transition-all text-[10px] font-bold uppercase tracking-widest text-white">
                            Change
                        </label>
                        <input type="file" name="avatar" id="avatar" class="hidden" onchange="document.getElementById('avatar-preview').src = window.URL.createObjectURL(this.files[0])">
                    </div>
                    <h3 class="font-bold text-xl mb-1">{{ $user->display_name ?? $user->name }}</h3>
                    <p class="text-[10px] font-bold text-[#8B5CF6] uppercase tracking-widest">{{ auth()->user()->role->title }}</p>
                </div>

                <div class="p-8 rounded-[2.5rem] glass-panel border-white/5 space-y-6">
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-white/40">Core Details</h4>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-white/30">Display Name</label>
                            <input type="text" name="display_name" value="{{ old('display_name', $user->display_name) }}"
                                   class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3 text-white focus:ring-2 focus:ring-[#8B5CF6]/50 outline-none transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-white/30">Timezone</label>
                            <select name="timezone" class="w-full bg-black/40 border border-white/10 rounded-2xl px-5 py-3 text-white appearance-none cursor-pointer">
                                @foreach(['UTC', 'CET', 'EST', 'PST', 'GMT'] as $tz)
                                    <option value="{{ $tz }}" {{ $user->timezone == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Bio & Games --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Bio --}}
                <div class="p-10 rounded-[3rem] glass-panel border-white/5 space-y-6">
                    <h4 class="text-xs font-black uppercase tracking-[0.2em] text-white/40">About Me</h4>
                    <textarea name="bio" rows="4" placeholder="Share your gaming journey..."
                              class="w-full bg-black/40 border border-white/10 rounded-[2rem] px-8 py-6 text-white placeholder:text-white/20 focus:ring-2 focus:ring-[#8B5CF6]/50 outline-none transition-all resize-none">{{ old('bio', $ebuddy->bio ?? '') }}</textarea>
                </div>

                {{-- Games & Ranks --}}
                <div class="p-10 rounded-[3rem] glass-panel border-white/5 space-y-8">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] text-white/40">Competitive Skillset</h4>
                        <button type="button" @click="selectedGames.push('')" class="text-[10px] font-bold text-[#8B5CF6] hover:underline uppercase tracking-widest">+ Add Game</button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(selectedGameId, index) in selectedGames" :key="index">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6 rounded-2xl bg-white/[0.03] border border-white/5 relative group">
                                <div class="space-y-2">
                                    <label class="text-[9px] font-bold uppercase tracking-widest text-white/30">Select Game</label>
                                    <select :name="'games['+index+'][game_id]'" x-model="selectedGames[index]"
                                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white appearance-none cursor-pointer">
                                        <option value="">-- Choose Game --</option>
                                        <template x-for="game in games">
                                            <option :value="game.id" x-text="game.title" :selected="game.id == selectedGameId"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[9px] font-bold uppercase tracking-widest text-white/30">Your Rank</label>
                                    <select :name="'games['+index+'][rank_id]'"
                                            class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white appearance-none cursor-pointer">
                                        <option value="">-- Select Rank --</option>
                                        <template x-for="rank in getAvailableRanks(selectedGames[index])">
                                            <option :value="rank.id" x-text="rank.title"></option>
                                        </template>
                                    </select>
                                </div>
                                <button type="button" @click="selectedGames.splice(index, 1)" 
                                        class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center text-[10px] opacity-0 group-hover:opacity-100 transition-all hover:bg-red-500 hover:text-white">✕</button>
                            </div>
                        </template>

                        <div x-show="selectedGames.length === 0" class="text-center py-10 text-white/20 text-xs italic">
                            No games added yet. Showcase your skills!
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-12 py-5 rounded-2xl bg-white text-black font-bold hover:scale-105 transition-all shadow-2xl shadow-white/10">Save Lounge Profile</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

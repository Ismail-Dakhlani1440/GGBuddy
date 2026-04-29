<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        {{ $title ?? config('app.name', 'GGBuddy') }}
        {{ isset($titleSuffix) ? ' · ' . $titleSuffix : '' }}
    </title>

    {{-- GGbuddy favicon/logo --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('ggbuddy-logo.svg') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen text-[#EDEDEC] bg-[#030305] relative overflow-x-hidden overflow-y-hidden font-['Inter'] selection:bg-[#8B5CF6]/30">
    {{-- Ambient Background --}}
    <div class="fixed inset-0 z-0">
        {{-- Deep grid --}}
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCI+PHBhdGggZD0iTTAgMGg0MHY0MEgwem0yMCAyMGgyMHYyMEgyMHptLTIwIDIwaDIwdjIwSDB6IiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDIpIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz48L3N2Zz4=')] opacity-30"></div>
        
        {{-- Top Right Glow --}}
        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-[#A855F7]/10 rounded-full blur-[120px] mix-blend-screen translate-x-1/3 -translate-y-1/4"></div>
        
        {{-- Bottom Left Glow --}}
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-[#8B5CF6]/15 rounded-full blur-[100px] mix-blend-screen -translate-x-1/3 translate-y-1/3"></div>
        
        {{-- Center Accent --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1000px] h-[500px] bg-[#C084FC]/5 rounded-full blur-[100px] pointer-events-none"></div>
    </div>

    <div class="relative z-10 flex flex-col h-full">
        {{-- Top bar --}}
        <header class="w-full px-6 py-4 lg:px-10 border-b border-white/5 bg-black/10 backdrop-blur-md">
            <div class="mx-auto max-w-7xl flex items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-3 select-none group">
                    <div class="relative">
                        <div class="absolute inset-0 bg-[#A855F7] blur-lg opacity-40 group-hover:opacity-70 transition-opacity duration-300"></div>
                        <img src="{{ asset('ggbuddy-logo.svg') }}"
                             alt="GGbuddy"
                             class="relative w-10 h-10 drop-shadow-xl" />
                    </div>
                    <div class="leading-tight">
                        <div class="text-xl font-bold tracking-tight">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#C084FC] to-[#8B5CF6]">GGbuddy</span>
                            <span class="text-white/90">Lounge</span>
                        </div>
                    </div>
                </a>

                {{-- Nav --}}
                @if (\Illuminate\Support\Facades\Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-medium border border-white/10 bg-white/[0.03] backdrop-blur-md
                                      hover:bg-white/[0.08] hover:border-white/20 transition-all duration-200">
                                <span class="text-[#A855F7] text-lg">⌁</span>
                                Dashboard
                            </a>

                            <form method="POST" action="{{ url('/logout') }}">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-medium border border-transparent hover:bg-white/[0.05] text-white/70 hover:text-white transition-all duration-200">
                                    <span class="text-[#C084FC] text-lg">⟲</span>
                                    Log out
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                               class="text-sm font-medium text-white/70 hover:text-white transition-colors px-4 py-2">
                                Log in
                            </a>

                            @if (\Illuminate\Support\Facades\Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-medium
                                          bg-white text-black hover:bg-white/90 shadow-[0_0_20px_rgba(255,255,255,0.15)]
                                          transition-all duration-200 transform hover:-translate-y-0.5">
                                    Register Now
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 flex flex-col items-center justify-center p-6 lg:p-10 min-h-0 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</body>
</html>

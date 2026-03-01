<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LeaveManager — Streamline Your Workforce</title>
    @vite(['resources/css/app.css'])
    <style>
        /* ── Base ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { background: #030712; color: #e2e8f0; overflow-x: hidden; }

        /* ── Noise texture overlay ────────────────────────────── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            background-size: 256px 256px;
            pointer-events: none;
            z-index: 999;
            opacity: .5;
        }

        /* ── Dots grid ────────────────────────────────────────── */
        .dots {
            background-image: radial-gradient(rgba(99,102,241,.18) 1px, transparent 1px);
            background-size: 30px 30px;
        }

        /* ── Gradient text ────────────────────────────────────── */
        .g-text {
            background: linear-gradient(135deg, #818cf8 0%, #60a5fa 45%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Glass card ───────────────────────────────────────── */
        .glass {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            backdrop-filter: blur(12px);
        }
        .glass-hover {
            transition: background .3s, border-color .3s, transform .4s cubic-bezier(.16,1,.3,1), box-shadow .4s;
        }
        .glass-hover:hover {
            background: rgba(255,255,255,.07);
            border-color: rgba(99,102,241,.4);
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 24px 48px rgba(99,102,241,.15), 0 0 0 1px rgba(99,102,241,.2);
        }

        /* ── Glow blobs ───────────────────────────────────────── */
        .glow-blob { filter: blur(80px); border-radius: 50%; position: absolute; pointer-events: none; }

        /* ── Floating animation ───────────────────────────────── */
        @keyframes float {
            0%,100% { transform: translateY(0) rotate(0deg); }
            40%      { transform: translateY(-18px) rotate(2deg); }
            70%      { transform: translateY(8px) rotate(-1.5deg); }
        }
        .float  { animation: float 9s ease-in-out infinite; }
        .float2 { animation: float 12s ease-in-out infinite; animation-delay: -4s; }
        .float3 { animation: float 7s ease-in-out infinite; animation-delay: -2s; }

        /* ── Shimmer ──────────────────────────────────────────── */
        @keyframes shimmer {
            from { background-position: -300% center; }
            to   { background-position:  300% center; }
        }
        .shimmer-border {
            position: relative;
        }
        .shimmer-border::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            background: linear-gradient(90deg, transparent 20%, rgba(129,140,248,.6) 50%, transparent 80%);
            background-size: 300% auto;
            animation: shimmer 4s linear infinite;
            z-index: -1;
        }

        /* ── ZOOM-IN reveal ───────────────────────────────────── */
        .zoom {
            opacity: 0;
            transform: scale(.82) translateY(28px);
            transition: opacity .8s cubic-bezier(.16,1,.3,1),
                        transform .8s cubic-bezier(.16,1,.3,1);
        }
        .zoom.d1 { transition-delay: .08s; }
        .zoom.d2 { transition-delay: .18s; }
        .zoom.d3 { transition-delay: .28s; }
        .zoom.d4 { transition-delay: .38s; }
        .zoom.d5 { transition-delay: .48s; }
        .zoom.d6 { transition-delay: .58s; }
        .zoom.d7 { transition-delay: .68s; }
        .zoom.in  { opacity: 1; transform: scale(1) translateY(0); }

        /* ── Hero zoom-in on load ────────────────────────────── */
        @keyframes heroLoad {
            from { opacity:0; transform: scale(.9) translateY(20px); }
            to   { opacity:1; transform: scale(1) translateY(0); }
        }
        .hero-in { animation: heroLoad 1.2s cubic-bezier(.16,1,.3,1) both; }
        .hero-in.d1 { animation-delay:.1s; }
        .hero-in.d2 { animation-delay:.25s; }
        .hero-in.d3 { animation-delay:.4s; }
        .hero-in.d4 { animation-delay:.6s; }

        /* ── 3D card perspective ─────────────────────────────── */
        .perspective { perspective: 800px; }
        .card-3d {
            transition: transform .5s cubic-bezier(.16,1,.3,1), box-shadow .5s;
            transform-style: preserve-3d;
        }
        .card-3d:hover { box-shadow: 0 32px 64px rgba(0,0,0,.5), 0 0 0 1px rgba(99,102,241,.3); }

        /* ── Scroll indicator ────────────────────────────────── */
        @keyframes scrollBounce {
            0%,100% { transform: translateY(0); opacity: .6; }
            50%      { transform: translateY(6px); opacity: 1; }
        }
        .scroll-dot { animation: scrollBounce 1.8s ease-in-out infinite; }

        /* ── Section divider glow ────────────────────────────── */
        .divider-glow {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(99,102,241,.5) 50%, transparent);
        }

        /* ── Parallax layer ──────────────────────────────────── */
        .pl { will-change: transform; }

        /* ── Scrollbar ───────────────────────────────────────── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: #030712; }
        ::-webkit-scrollbar-thumb { background: #4f46e5; border-radius: 4px; }

        /* ── Navbar transition ───────────────────────────────── */
        #nav {
            transition: background .4s, border-color .4s, backdrop-filter .4s;
            border-bottom: 1px solid transparent;
        }
        #nav.solid {
            background: rgba(3,7,18,.85);
            border-color: rgba(255,255,255,.07);
            backdrop-filter: blur(20px);
        }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased">

{{-- ══════════════════════════════════════════════════════════
     NAVBAR
══════════════════════════════════════════════════════════ --}}
<nav id="nav" class="fixed top-0 inset-x-0 z-50">
    <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-900/60 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="text-white font-bold text-lg tracking-tight">LeaveManager</span>
        </a>
        <a href="{{ route('login') }}"
           class="relative overflow-hidden bg-indigo-600 text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-indigo-500 active:scale-95 transition-all shadow-lg shadow-indigo-900/50">
            Sign In
        </a>
    </div>
</nav>

{{-- ══════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════ --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden" style="background:#030712;">

    {{-- Parallax background --}}
    <div id="hero-bg" class="pl absolute inset-0 pointer-events-none">
        <div class="dots absolute inset-0 opacity-70"></div>
        <div class="glow-blob float  w-[600px] h-[600px] bg-indigo-600/25 -top-32 -left-32"></div>
        <div class="glow-blob float2 w-[500px] h-[500px] bg-violet-600/20 top-1/2 -right-40"></div>
        <div class="glow-blob float3 w-[400px] h-[400px] bg-blue-500/15 -bottom-20 left-1/3"></div>
        {{-- Radial vignette --}}
        <div class="absolute inset-0" style="background:radial-gradient(ellipse 80% 60% at 50% 40%, transparent 30%, #030712 100%)"></div>
    </div>

    {{-- Floating UI cards --}}
    <div id="card-a" class="pl float absolute top-36 right-10 xl:right-24 hidden lg:block pointer-events-none">
        <div class="glass shimmer-border rounded-2xl p-4 w-52">
            <p class="text-xs text-indigo-400 font-medium mb-2">Annual Leave Balance</p>
            <p class="text-3xl font-extrabold text-white mb-1">12 <span class="text-sm font-normal text-gray-400">days left</span></p>
            <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden mt-2">
                <div class="h-full w-3/5 rounded-full" style="background:linear-gradient(90deg,#6366f1,#60a5fa)"></div>
            </div>
        </div>
    </div>
    <div id="card-b" class="pl float2 absolute bottom-40 left-8 xl:left-24 hidden lg:block pointer-events-none">
        <div class="glass shimmer-border rounded-2xl px-4 py-3 w-48">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white text-xs font-semibold">Leave Approved</p>
                    <p class="text-indigo-400 text-xs">Just now</p>
                </div>
            </div>
        </div>
    </div>
    <div id="card-c" class="pl float3 absolute top-1/2 left-6 xl:left-16 -translate-y-1/2 hidden xl:block pointer-events-none">
        <div class="glass rounded-xl px-3 py-2.5 w-40">
            <p class="text-xs text-gray-400 mb-1.5">Pending Reviews</p>
            <div class="flex items-end gap-1.5 h-10">
                @foreach([40,65,45,80,55,90,70] as $h)
                <div class="flex-1 rounded-sm" style="height:{{ $h }}%;background:rgba(99,102,241,.5)"></div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Hero content --}}
    <div class="relative z-10 text-center px-6 max-w-4xl mx-auto pt-20">
        <div class="hero-in inline-flex items-center gap-2 glass rounded-full px-4 py-1.5 mb-8 text-xs font-medium text-indigo-300">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            Production-ready &nbsp;&bull;&nbsp; Laravel 12 &nbsp;&bull;&nbsp; Multi-role
        </div>

        <h1 class="hero-in d1 text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-extrabold leading-[1.03] tracking-tight text-white mb-6">
            Leave Requests,<br>
            <span class="g-text">Simplified.</span>
        </h1>

        <p class="hero-in d2 text-gray-400 text-lg md:text-xl max-w-xl mx-auto leading-relaxed mb-10">
            A modern, role-aware leave management system built for real teams —
            from employees submitting requests to managers approving them in seconds.
        </p>

        <div class="hero-in d3 flex flex-wrap justify-center gap-4">
            <a href="{{ route('login') }}"
               class="group flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-8 py-4 rounded-xl active:scale-95 transition-all shadow-2xl shadow-indigo-900/60">
                Sign In to Dashboard
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            <a href="#features"
               class="flex items-center gap-2 glass hover:bg-white/8 text-white font-medium px-8 py-4 rounded-xl active:scale-95 transition-all">
                Explore Features
            </a>
        </div>

        {{-- Scroll indicator --}}
        <div class="hero-in d4 mt-16 flex flex-col items-center gap-2 text-gray-600 text-xs">
            <span>Scroll to explore</span>
            <div class="w-5 h-8 border border-gray-700 rounded-full flex justify-center pt-1.5">
                <div class="w-1 h-1.5 bg-indigo-500 rounded-full scroll-dot"></div>
            </div>
        </div>
    </div>
</section>

<div class="divider-glow"></div>

{{-- ══════════════════════════════════════════════════════════
     FEATURES
══════════════════════════════════════════════════════════ --}}
<section id="features" class="relative py-28" style="background:#030712;">
    <div class="glow-blob absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-64 bg-indigo-600/10 pointer-events-none" style="filter:blur(60px)"></div>

    <div class="relative max-w-6xl mx-auto px-6">
        <div class="text-center mb-16 zoom">
            <p class="text-indigo-400 text-sm font-semibold uppercase tracking-widest mb-3">Features</p>
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-4">Everything your team needs</h2>
            <p class="text-gray-500 text-lg max-w-md mx-auto">Four roles, full visibility, seamless workflows — all in one place.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            @foreach ([
                ['icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                 'glow'=>'#6366f1','title'=>'Leave Types',
                 'desc'=>'Annual, medical, maternity and more — carry-forward, max days, and attachment rules per type.'],
                ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                 'glow'=>'#8b5cf6','title'=>'Role Management',
                 'desc'=>'Admin, HR, Manager, Employee — 15 Spatie permissions wired to every action in the system.'],
                ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                 'glow'=>'#10b981','title'=>'Smart Approvals',
                 'desc'=>'One-click approve or reject. Balance auto-deducted, overlaps blocked, weekends excluded.'],
                ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                 'glow'=>'#3b82f6','title'=>'Reports & Analytics',
                 'desc'=>'Filter by year, month, status. Visual summaries for pending, approved and rejected across your org.'],
            ] as $i => $f)
            <div class="zoom d{{ $i+1 }} perspective">
                <div class="card-3d glass glass-hover rounded-2xl p-6 h-full">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5" style="background:{{ $f['glow'] }}20;box-shadow:0 0 20px {{ $f['glow'] }}30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color:{{ $f['glow'] }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-white mb-2">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid md:grid-cols-3 gap-4">
            @foreach ([
                ['icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                 'glow'=>'#f59e0b','title'=>'Email Notifications',
                 'desc'=>'Database-queued notifications. Managers notified on submission; employees on decision. No Redis required.'],
                ['icon'=>'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13',
                 'glow'=>'#ef4444','title'=>'File Attachments',
                 'desc'=>'Medical certs, supporting docs — PDF/JPG/PNG up to 2 MB. Required per leave type, validated server-side.'],
                ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                 'glow'=>'#14b8a6','title'=>'Policy-Driven Auth',
                 'desc'=>'Every action gated by Laravel Policies. Self-approval blocked. Admins bypass all via Gate::before.'],
            ] as $i => $f)
            <div class="zoom d{{ $i+1 }} perspective">
                <div class="card-3d glass glass-hover rounded-2xl p-6 h-full">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5" style="background:{{ $f['glow'] }}20;box-shadow:0 0 20px {{ $f['glow'] }}30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color:{{ $f['glow'] }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-white mb-2">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<div class="divider-glow"></div>

{{-- ══════════════════════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════════════════════ --}}
<section class="relative py-28 overflow-hidden" style="background:#050816;">
    <div id="steps-bg" class="pl absolute inset-0 pointer-events-none">
        <div class="dots absolute inset-0 opacity-50"></div>
        <div class="glow-blob float  w-96 h-96 bg-violet-600/15 -top-20 right-0"></div>
        <div class="glow-blob float2 w-80 h-80 bg-indigo-500/10 bottom-0 left-0"></div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto px-6">
        <div class="text-center mb-16 zoom">
            <p class="text-indigo-400 text-sm font-semibold uppercase tracking-widest mb-3">How It Works</p>
            <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-4">Three steps to approved</h2>
            <p class="text-gray-500 text-lg max-w-sm mx-auto">From submission to inbox notification in minutes.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach ([
                ['title'=>'Submit a Request',
                 'desc'=>'Pick leave type, select dates, attach a doc if needed. Working days calculated automatically — weekends always excluded.',
                 'icon'=>'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                 'color'=>'#6366f1'],
                ['title'=>'Manager Reviews',
                 'desc'=>"Lands in the approval panel instantly. Manager sees balance, history and request details — approve or reject in one click.",
                 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                 'color'=>'#8b5cf6'],
                ['title'=>'Balance Updated',
                 'desc'=>'Approval auto-deducts the balance and sends the employee an email notification. All logged, visible in reports.',
                 'icon'=>'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                 'color'=>'#10b981'],
            ] as $i => $s)
            <div class="zoom d{{ $i+1 }} relative text-center">
                @if ($i < 2)
                <div class="hidden md:block absolute top-7 left-[calc(50%+36px)] right-0 h-px" style="background:linear-gradient(90deg,rgba(99,102,241,.5),transparent)"></div>
                @endif
                <div class="inline-flex relative items-center justify-center w-14 h-14 rounded-2xl mb-6" style="background:{{ $s['color'] }}18;box-shadow:0 0 30px {{ $s['color'] }}25;border:1px solid {{ $s['color'] }}30">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="color:{{ $s['color'] }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $s['icon'] }}"/>
                    </svg>
                    <span class="absolute -top-2 -right-2 w-5 h-5 rounded-full text-white text-[10px] font-bold flex items-center justify-center" style="background:{{ $s['color'] }}">{{ $i+1 }}</span>
                </div>
                <h3 class="text-white font-bold text-lg mb-3">{{ $s['title'] }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

<div class="divider-glow"></div>

{{-- ══════════════════════════════════════════════════════════
     STATS
══════════════════════════════════════════════════════════ --}}
<section class="relative py-24 overflow-hidden" style="background:#030712;">
    <div class="glow-blob absolute inset-x-0 top-0 h-64 bg-indigo-600/8 mx-auto w-full pointer-events-none" style="filter:blur(60px)"></div>

    <div class="relative max-w-5xl mx-auto px-6">
        <div class="zoom text-center mb-14">
            <p class="text-indigo-400 text-sm font-semibold uppercase tracking-widest mb-3">By the Numbers</p>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white">Built for real organisations</h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['num'=>'4',  'color'=>'#6366f1','label'=>'Access Roles',       'sub'=>'Admin · HR · Manager · Employee'],
                ['num'=>'6',  'color'=>'#8b5cf6','label'=>'Default Leave Types', 'sub'=>'Annual, Medical, Maternity & more'],
                ['num'=>'15', 'color'=>'#3b82f6','label'=>'Permissions',         'sub'=>'Fine-grained policy control'],
                ['num'=>'∞',  'color'=>'#10b981','label'=>'Queue Jobs',           'sub'=>'Database-driven, no Redis'],
            ] as $i => $s)
            <div class="zoom d{{ $i+1 }} glass rounded-2xl p-6 text-center" style="box-shadow:0 0 40px {{ $s['color'] }}10">
                <div class="text-5xl font-extrabold mb-2" style="color:{{ $s['color'] }}">{{ $s['num'] }}</div>
                <div class="text-white font-semibold text-sm mb-1">{{ $s['label'] }}</div>
                <div class="text-gray-600 text-xs leading-snug">{{ $s['sub'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<div class="divider-glow"></div>

{{-- ══════════════════════════════════════════════════════════
     ROLES
══════════════════════════════════════════════════════════ --}}
<section class="relative py-24 overflow-hidden" style="background:#050816;">
    <div id="roles-bg" class="pl absolute inset-0 pointer-events-none">
        <div class="dots absolute inset-0 opacity-40"></div>
        <div class="glow-blob float  w-80 h-80 bg-violet-500/10 top-0 left-0"></div>
        <div class="glow-blob float2 w-96 h-96 bg-indigo-500/10 bottom-0 right-0"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-6">
        <div class="text-center mb-14 zoom">
            <p class="text-indigo-400 text-sm font-semibold uppercase tracking-widest mb-3">Access Roles</p>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">The right access for every person</h2>
            <p class="text-gray-500 text-lg max-w-md mx-auto">One codebase, four perspectives — each tailored to what that role actually needs.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['role'=>'Admin',    'color'=>'#6366f1','badge'=>'rgba(99,102,241,.15)',
                 'perks'=>['Full system access','Bypasses all policies','User & role management','Reports & analytics']],
                ['role'=>'HR',       'color'=>'#8b5cf6','badge'=>'rgba(139,92,246,.15)',
                 'perks'=>['Manage leave types','Create & edit users','View all requests','Access reports']],
                ['role'=>'Manager',  'color'=>'#3b82f6','badge'=>'rgba(59,130,246,.15)',
                 'perks'=>['Approve & reject leave','Approval panel dashboard','View reports','Cannot self-approve']],
                ['role'=>'Employee', 'color'=>'#10b981','badge'=>'rgba(16,185,129,.15)',
                 'perks'=>['Submit leave requests','View own history','Track leave balance','Email notifications']],
            ] as $i => $r)
            <div class="zoom d{{ $i+1 }} perspective">
                <div class="card-3d glass rounded-2xl overflow-hidden h-full">
                    <div class="h-1 w-full" style="background:{{ $r['color'] }}"></div>
                    <div class="p-6">
                        <span class="inline-block text-xs font-semibold px-3 py-1 rounded-full mb-5" style="background:{{ $r['badge'] }};color:{{ $r['color'] }}">{{ $r['role'] }}</span>
                        <ul class="space-y-3">
                            @foreach ($r['perks'] as $p)
                            <li class="flex items-start gap-2.5 text-sm text-gray-400">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color:{{ $r['color'] }}">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $p }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<div class="divider-glow"></div>

{{-- ══════════════════════════════════════════════════════════
     CTA
══════════════════════════════════════════════════════════ --}}
<section class="relative py-32 overflow-hidden" style="background:#030712;">
    <div id="cta-bg" class="pl absolute inset-0 pointer-events-none">
        <div class="dots absolute inset-0 opacity-60"></div>
        <div class="glow-blob float  w-[500px] h-[500px] bg-indigo-600/20 -top-20 -right-20"></div>
        <div class="glow-blob float2 w-[500px] h-[500px] bg-violet-600/15 -bottom-20 -left-20"></div>
        <div class="absolute inset-0" style="background:radial-gradient(ellipse 70% 50% at 50% 50%, rgba(99,102,241,.06) 0%, transparent 70%)"></div>
    </div>

    <div class="relative z-10 max-w-2xl mx-auto px-6 text-center">
        <div class="zoom">
            <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-1.5 mb-8 text-xs font-medium text-emerald-400">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Ready to deploy on shared hosting
            </div>
            <h2 class="text-4xl md:text-6xl font-extrabold text-white mb-5 leading-tight">
                Start managing leave<br>
                <span class="g-text">the right way.</span>
            </h2>
            <p class="text-gray-500 text-lg mb-10 leading-relaxed">
                Sign in with a demo account and explore every feature — no setup required if you're running locally.
            </p>
            <a href="{{ route('login') }}"
               class="group inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold px-10 py-4 rounded-xl active:scale-95 transition-all shadow-2xl shadow-indigo-900/60 mb-10">
                Sign In
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>

            {{-- Demo credentials --}}
            <div class="glass rounded-2xl px-6 py-5 text-left inline-block w-full max-w-sm mx-auto">
                <p class="text-indigo-400 text-xs font-semibold uppercase tracking-widest mb-4">Demo Credentials</p>
                <div class="space-y-3">
                    @foreach ([
                        ['Admin',    'admin@company.com',    'Admin@123',    '#6366f1'],
                        ['HR',       'hr@company.com',       'HR@1234!',     '#8b5cf6'],
                        ['Manager',  'manager@company.com',  'Manager@123',  '#3b82f6'],
                        ['Employee', 'employee@company.com', 'Employee@123', '#10b981'],
                    ] as $c)
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-xs px-2 py-0.5 rounded-md" style="background:{{ $c[3] }}20;color:{{ $c[3] }}">{{ $c[0] }}</span>
                        <div class="text-right">
                            <div class="text-gray-300 text-xs font-mono">{{ $c[1] }}</div>
                            <div class="text-gray-600 text-xs font-mono">{{ $c[2] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════════════ --}}
<footer style="background:#010208;border-top:1px solid rgba(255,255,255,.05);">
    <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-600">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 bg-indigo-600 rounded-md flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="text-gray-400 font-semibold">LeaveManager</span>
        </div>
        <span>Laravel 12 &bull; TailwindCSS &bull; Spatie Permission</span>
        <span>&copy; {{ date('Y') }} GhProfit. All rights reserved.</span>
    </div>
</footer>

{{-- ══════════════════════════════════════════════════════════
     JAVASCRIPT — Parallax + Zoom Reveal
══════════════════════════════════════════════════════════ --}}
<script>
(function () {
    /* ── Refs ──────────────────────────────────────── */
    const nav      = document.getElementById('nav');
    const heroBg   = document.getElementById('hero-bg');
    const cardA    = document.getElementById('card-a');
    const cardB    = document.getElementById('card-b');
    const cardC    = document.getElementById('card-c');
    const stepsBg  = document.getElementById('steps-bg');
    const rolesBg  = document.getElementById('roles-bg');
    const ctaBg    = document.getElementById('cta-bg');

    /* ── Zoom-in reveal (IntersectionObserver) ─────── */
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('in');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.10 });
    document.querySelectorAll('.zoom').forEach(el => io.observe(el));

    /* ── Scroll parallax (rAF) ─────────────────────── */
    let sy = 0, ticking = false;

    function sectionOffset(section) {
        if (!section) return 0;
        const r = section.closest('section').getBoundingClientRect();
        return (window.innerHeight - r.top) * 0.12;
    }

    function tick() {
        /* Navbar */
        if (sy > 50) nav.classList.add('solid');
        else          nav.classList.remove('solid');

        /* Hero: background drifts at 30% scroll speed */
        if (heroBg) heroBg.style.transform = `translateY(${sy * 0.30}px)`;

        /* Floating cards: move upward at different rates for depth */
        if (cardA) cardA.style.transform = `translateY(${sy * -0.20}px)`;
        if (cardB) cardB.style.transform = `translateY(${sy * -0.13}px)`;
        if (cardC) cardC.style.transform = `translateY(${sy * -0.08}px)`;

        /* Background parallax on dark sections */
        if (stepsBg) stepsBg.style.transform = `translateY(${sectionOffset(stepsBg)}px)`;
        if (rolesBg) rolesBg.style.transform  = `translateY(${sectionOffset(rolesBg)}px)`;
        if (ctaBg)   ctaBg.style.transform    = `translateY(${sectionOffset(ctaBg)}px)`;

        ticking = false;
    }

    window.addEventListener('scroll', () => {
        sy = window.scrollY;
        if (!ticking) { requestAnimationFrame(tick); ticking = true; }
    }, { passive: true });

    /* ── 3D tilt on card hover ─────────────────────── */
    document.querySelectorAll('.card-3d').forEach(card => {
        card.addEventListener('mousemove', e => {
            const r  = card.getBoundingClientRect();
            const cx = r.left + r.width  / 2;
            const cy = r.top  + r.height / 2;
            const rx = ((e.clientY - cy) / (r.height / 2)) * -6;
            const ry = ((e.clientX - cx) / (r.width  / 2)) *  6;
            card.style.transform = `perspective(800px) rotateX(${rx}deg) rotateY(${ry}deg) scale(1.03)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });

    tick();
}());
</script>

</body>
</html>

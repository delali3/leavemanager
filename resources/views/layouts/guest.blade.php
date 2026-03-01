<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Leave Manager') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen flex">

    {{-- ===== Left branding panel ===== --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 bg-indigo-900 flex-col justify-between p-12 relative overflow-hidden">

        {{-- Decorative blobs --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-800 rounded-full opacity-60"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-indigo-700 rounded-full opacity-50"></div>
        <div class="absolute top-1/2 right-8 w-32 h-32 bg-white/5 rounded-full"></div>

        {{-- Brand --}}
        <div class="relative z-10 flex items-center gap-3">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-white text-lg font-bold tracking-wide">Leave Manager</span>
        </div>

        {{-- Hero text --}}
        <div class="relative z-10 space-y-6">
            <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight">
                Manage leave,<br>simplified.
            </h1>
            <p class="text-indigo-300 text-base leading-relaxed max-w-sm">
                A streamlined platform for employees, managers, and HR to handle leave requests effortlessly.
            </p>
            <ul class="space-y-4 pt-2">
                @foreach([
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'text' => 'Apply for leave in seconds'],
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'text' => 'Real-time approval workflow'],
                    ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'text' => 'Balance tracking & reports'],
                ] as $item)
                <li class="flex items-center gap-3">
                    <span class="w-8 h-8 bg-indigo-700 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="text-indigo-200 text-sm">{{ $item['text'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 text-indigo-500 text-xs">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>

    {{-- ===== Right form panel ===== --}}
    <div class="flex-1 flex flex-col justify-center items-center bg-gray-50 px-6 py-12 sm:px-10">

        {{-- Mobile logo --}}
        <div class="lg:hidden mb-8 flex flex-col items-center gap-2">
            <div class="w-12 h-12 bg-indigo-900 rounded-xl flex items-center justify-center shadow">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-indigo-900 font-bold text-lg tracking-wide">Leave Manager</span>
        </div>

        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

</div>

</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Geni') }} — Admin</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">
            {{-- Left branding panel --}}
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 flex-col justify-between p-12 relative overflow-hidden">
                <div class="absolute inset-0 pointer-events-none">
                    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-600 opacity-20 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 right-0 w-80 h-80 bg-purple-600 opacity-20 rounded-full blur-3xl"></div>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-white text-2xl font-bold tracking-tight">{{ config('app.name') }}</span>
                    </div>
                </div>
                <div class="relative z-10 space-y-6">
                    <h1 class="text-4xl font-bold text-white leading-snug">Manage everything<br/>from one place.</h1>
                    <p class="text-slate-300 text-lg leading-relaxed max-w-sm">Your all-in-one admin panel to monitor, manage, and grow your application with ease.</p>
                    <div class="space-y-3 pt-2">
                        @foreach(['Real-time analytics & insights', 'User & role management', 'Secure & role-based access'] as $feature)
                        <div class="flex items-center gap-3">
                            <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span class="text-slate-300 text-sm">{{ $feature }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="relative z-10">
                    <p class="text-slate-500 text-sm">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>

            {{-- Right login panel --}}
            <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 px-6 py-12">
                <div class="w-full max-w-md">
                    <div class="flex items-center gap-3 mb-10 lg:hidden">
                        <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-slate-800 text-xl font-bold">{{ config('app.name') }}</span>
                    </div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

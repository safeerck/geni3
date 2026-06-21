<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="text-center px-6">
            <h1 class="text-4xl font-semibold text-gray-800 mb-3">
                Welcome to {{ config('app.name', 'Laravel') }}
            </h1>
            <p class="text-gray-500 text-lg mb-8">
                Your application is up and running.
            </p>
            @if (Route::has('login'))
                <div class="flex items-center justify-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </body>
</html>

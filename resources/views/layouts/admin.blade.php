<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Admin — {{ config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex">
            {{-- Sidebar --}}
            <aside class="w-64 bg-gray-900 text-white flex flex-col">
                <div class="px-6 py-5 border-b border-gray-700">
                    <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg">
                        {{ config('app.name') }} Admin
                    </a>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">Users</a>
                    <hr class="border-gray-700 my-2">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded text-sm text-gray-400 hover:bg-gray-700">← Back to App</a>
                </nav>
            </aside>

            {{-- Main --}}
            <div class="flex-1 flex flex-col">
                <header class="bg-white shadow px-6 py-4 flex items-center justify-between">
                    <h1 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Admin' }}</h1>
                    <span class="text-sm text-gray-500">{{ Auth::user()->name }}</span>
                </header>
                <main class="flex-1 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>

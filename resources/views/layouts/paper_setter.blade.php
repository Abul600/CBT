<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    <x-banner />
    
    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <main class="p-6">
            @yield('content')
        </main>
    </div>

    @stack('modals')
    @livewireScripts
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-lg font-bold">Dashboard</a>
            
            <div class="flex items-center space-x-4">
                <span>{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-red-500 px-3 py-1 rounded text-white hover:bg-red-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-6">
        @yield('content')
    </main>
</body>
</html>

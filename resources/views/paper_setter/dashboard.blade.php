<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paper Setter Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">Welcome, Paper Setter!</h1>
                <h3 class="text-lg font-bold mb-4">Paper Setter Panel</h3>
                <p class="mb-4">Manage exam questions efficiently.</p>

                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('paper_setter.questions.index') }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block">
                            📝 Manage Questions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboard') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded inline-block">
                            🏠 Go to Main Dashboard
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

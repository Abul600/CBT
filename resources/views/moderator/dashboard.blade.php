<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Moderator Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Welcome, {{ auth()->user()->name }}! You are logged in as a Moderator.</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Manage Paper Seaters -->
                    <a href="{{ route('moderator.paper_seaters.index') }}" class="block px-4 py-2 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-700">
                        Manage Paper Seaters
                    </a>

                    <!-- Manage Exams -->
                    <a href="{{ route('moderator.exams.index') }}" class="block px-4 py-2 bg-green-500 text-white text-center rounded-lg hover:bg-green-700">
                        Manage Exams
                    </a>

                    <!-- Manage Questions -->
                    <a href="{{ route('moderator.questions.index') }}" class="block px-4 py-2 bg-red-500 text-white text-center rounded-lg hover:bg-red-700">
                        Manage Questions
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

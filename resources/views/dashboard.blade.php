<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-welcome />
            </div>

            <!-- Show Manage Moderators button only for Admins -->
            @if(auth()->user()->role === 'admin')
                <div class="mt-4 p-4 bg-white shadow sm:rounded-lg text-center">
                    <a href="{{ route('admin.moderators.index') }}" 
                        class="px-4 py-2 bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                        Manage Moderators
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

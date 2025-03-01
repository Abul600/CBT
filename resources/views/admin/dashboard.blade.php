<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Admin Panel</h3>

                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('admin.moderators.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded inline-block">
                            Manage Moderators
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

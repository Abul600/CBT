<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">Welcome, Admin!</h1>
                <h3 class="text-lg font-bold mb-4">Admin Panel</h3>
                <p class="mb-4">Here you can manage the system.</p>

                <ul class="space-y-3">
                    <li>
                        <a href="{{ route('admin.moderators.index') }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded flex items-center">
                            📌 Manage Moderators
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.exams.index') }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center">
                            📝 Manage Exams
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" 
                           class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded flex items-center">
                            👥 Manage Users
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>

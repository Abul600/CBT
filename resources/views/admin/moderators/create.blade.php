<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Moderator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.moderators.store') }}">
                    @csrf
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" class="border rounded p-2 w-full" required>
                    </div>
                    <div class="mt-4">
                        <label>Email</label>
                        <input type="email" name="email" class="border rounded p-2 w-full" required>
                    </div>
                    <div class="mt-4">
                        <label>Password</label>
                        <input type="password" name="password" class="border rounded p-2 w-full" required>
                    </div>
                    <div class="mt-4">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="border rounded p-2 w-full" required>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Add Moderator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

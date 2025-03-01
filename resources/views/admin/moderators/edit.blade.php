<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Moderator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.moderators.update', $moderator->id) }}">
                    @csrf
                    @method('PUT')
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" value="{{ $moderator->name }}" class="border rounded p-2 w-full" required>
                    </div>
                    <div class="mt-4">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ $moderator->email }}" class="border rounded p-2 w-full" required>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update Moderator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

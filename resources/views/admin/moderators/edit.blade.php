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

                    <!-- Name Field -->
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $moderator->name) }}" 
                            class="border rounded p-2 w-full focus:outline-none focus:ring focus:border-blue-300"
                            required autocomplete="off">
                        @error('name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="block font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $moderator->email) }}" 
                            class="border rounded p-2 w-full focus:outline-none focus:ring focus:border-blue-300"
                            required autocomplete="off">
                        @error('email')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                            Update Moderator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

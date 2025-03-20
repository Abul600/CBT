<x-app-layout>
    <x-slot name="header">
    <div class="bg-blue-500 p-4 rounded-md">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Edit Moderator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-600 shadow-2xl sm:rounded-lg p-6 border border-white/30">
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
                        <button type="submit" class="px-4 py-2 bg-yellow-400 text-black rounded hover:bg-yellow-300 transition">
                            Update Moderator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Paper Setter') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('moderator.paper_setters.store') }}" method="POST">
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="border-gray-300 rounded w-full @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="border-gray-300 rounded w-full @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Field -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="border-gray-300 rounded w-full @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- District Field (Readonly) -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">District</label>
                        <input type="text" name="district" value="{{ auth()->user()->district }}" readonly class="border-gray-300 rounded w-full bg-gray-100 cursor-not-allowed">
                    </div>

                    <!-- Hidden Moderator ID -->
                    <input type="hidden" name="moderator_id" value="{{ auth()->user()->id }}">

                    <!-- Password Field -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Password</label>
                        <input type="password" name="password" required class="border-gray-300 rounded w-full @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="border-gray-300 rounded w-full">
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            Add Paper Setter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

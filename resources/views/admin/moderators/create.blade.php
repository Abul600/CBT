<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-500 p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Add Moderator') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-600 
                        shadow-2xl sm:rounded-lg p-6 border border-white/30">
                <form method="POST" action="{{ route('admin.moderators.store') }}">
                    @csrf

                    <!-- Name Field -->
                    <div>
                        <label for="name" class="text-black font-semibold">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Enter Name" required>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mt-4">
                        <label for="email" class="text-black font-semibold">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Enter Email" required>
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mt-4">
                        <label for="password" class="text-black font-semibold">Password</label>
                        <input type="password" id="password" name="password" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Enter Password" required>
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mt-4">
                        <label for="password_confirmation" class="text-black font-semibold">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Confirm Password" required>
                    </div>

                    <!-- Role Selection Box -->
                    <div class="mt-4">
                        <label for="role" class="text-black font-semibold">Role</label>
                        <select id="role" name="role" class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                            <option value="">-- Select Role --</option>
                            <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                            <option value="paper setter" {{ old('role') == 'paper setter' ? 'selected' : '' }}>Paper Setter</option>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                        </select>
                        @error('role')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-yellow-400 text-gray-900 font-bold rounded hover:bg-yellow-300 transition">
                            Add Moderator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

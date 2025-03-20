<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-500 p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Create New User') }}
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
                    <div class="mb-4">
                        <label for="name" class="text-black font-semibold">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="form-input-style" 
                               placeholder="Enter Name" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mt-4">
                        <label for="email" class="text-black font-semibold">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="form-input-style" 
                               placeholder="Enter Email" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Number Field -->
                    <div class="mt-4">
                        <label class="text-black font-semibold">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="form-input-style" 
                               placeholder="Enter Phone Number" required>
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- District Dropdown -->
                    <div class="mt-4">
                        <label class="text-black font-semibold">District</label>
                        <select name="district" class="form-input-style" required>
                            <option value="" disabled selected>Select District</option>
                            <option value="Jorhat">Jorhat</option>
                            <option value="Golaghat">Golaghat</option>
                            <option value="Lakhimpur">Lakhimpur</option>
                            <option value="Dibrugarh">Dibrugarh</option>
                            <option value="Dhemaji">Dhemaji</option>
                            <option value="Sivasagar">Sivasagar</option>
                        </select>
                        @error('district')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="mt-4">
                        <label for="password" class="text-black font-semibold">Password</label>
                        <input type="password" id="password" name="password" 
                               class="form-input-style" 
                               placeholder="Enter Password" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="mt-4">
                        <label for="password_confirmation" class="text-black font-semibold">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="form-input-style" 
                               placeholder="Confirm Password" required>
                    </div>

                    <!-- Role Selection -->
                    <div class="mt-4">
                        <label for="role" class="text-black font-semibold">User Role</label>
                        <select id="role" name="role" class="form-input-style" required>
                            <option value="">-- Select Role --</option>
                            @foreach(['admin', 'moderator', 'paper_seater', 'student'] as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-primary">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .form-input-style {
                @apply border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                       focus:ring-2 focus:ring-yellow-400 focus:outline-none;
            }
            
            .error-message {
                @apply text-red-500 text-sm mt-1;
            }
            
            .btn-primary {
                @apply px-4 py-2 bg-yellow-400 text-gray-900 font-bold rounded hover:bg-yellow-300 
                       transition transform hover:scale-105;
            }
        </style>
    @endpush
</x-app-layout>

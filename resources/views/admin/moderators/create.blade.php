<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-500 p-4 rounded-md">
        <h2 class="font-semibold text-xl text-white leading-tight">
    👤 {{ __('Add Moderator') }}
</h2>

        </div>
    </x-slot>

    <div class="pt-8 pb-12"> <!-- Adjusted padding to move it upwards -->
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-600 
                        shadow-2xl sm:rounded-lg p-6 border border-white/30">
                <form method="POST" action="{{ route('admin.moderators.store') }}">
                    @csrf

                    <div>
                        <label class="text-black font-semibold">Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Enter Name" required>
                        @error('name')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="text-black font-semibold">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Enter Email" required>
                        @error('email')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Number Field -->
                    <div class="mt-4">
                        <label class="text-black font-semibold">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Enter Phone Number" required>
                        @error('phone')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- District Dropdown -->
                    <div class="mt-4">
                        <label class="text-black font-semibold">District</label>
                        <select name="district" class="border rounded p-2 w-full bg-white text-black 
                                                       focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                            <option value="" disabled selected>Select District</option>
                            <option value="Jorhat">Jorhat</option>
                            <option value="Golaghat">Golaghat</option>
                            <option value="Lakhimpur">Lakhimpur</option>
                            <option value="Dibrugarh">Dibrugarh</option>
                            <option value="Dhemaji">Dhemaji</option>
                            <option value="Sivasagar">Sivasagar</option>
                        </select>
                        @error('district')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="text-black font-semibold">Password</label>
                        <input type="password" name="password" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Enter Password" required>
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label class="text-black font-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation" 
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500 
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none" 
                               placeholder="Confirm Password" required>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="px-4 py-2 bg-yellow-400 text-gray-900 font-bold rounded 
                                                       hover:bg-yellow-300 transition">
                            Add Moderator
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

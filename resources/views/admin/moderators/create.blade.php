<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-500 p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                👤 {{ __('Add Moderator') }}
            </h2>
        </div>
    </x-slot>

    <div class="pt-0 pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
            <div class="bg-gradient-to-r from-cyan-500 via-blue-500 to-indigo-600 
                        shadow-2xl sm:rounded-lg p-6 border-4 border-white border-opacity-90 
                        bg-white/5">
                <form method="POST" action="{{ route('admin.moderators.store') }}">
                    @csrf

                    <!-- Name -->
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

                    <!-- Email -->
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

                    <!-- Phone Number -->
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

                    <!-- District -->
                    <div class="mt-4">
                        <label class="text-black font-semibold">District</label>
                        <select name="district_id"
                                class="border rounded p-2 w-full bg-white text-black placeholder-gray-500
                                       focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                                required>
                            <option value="" disabled {{ old('district_id') ? '' : 'selected' }}>Select District</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
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

                    <!-- Confirm Password -->
                    <div class="mt-4">
                        <label class="text-black font-semibold">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="border rounded p-2 w-full bg-white text-black placeholder-gray-500
                                      focus:ring-2 focus:ring-yellow-400 focus:outline-none"
                               placeholder="Confirm Password" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit"
                                class="mx-auto block px-6 py-3 min-w-[180px] text-white font-bold rounded-full
                                       bg-gradient-to-r from-red-500 via-yellow-400 via-green-400 via-blue-500 to-purple-600
                                       hover:from-purple-600 hover:via-blue-500 hover:via-green-400 hover:to-yellow-400
                                       shadow-lg transition duration-300 ease-in-out transform hover:scale-105">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

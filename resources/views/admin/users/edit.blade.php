<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between px-6 py-5 bg-white shadow-sm border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 8H7a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2z" />
                </svg>
                Edit User Role
            </h2>
            <span class="text-sm text-gray-500 italic">User: <strong>{{ $user->name }}</strong></span>
        </div>
    </x-slot>

    <div class="pt-6 pb-16 bg-gradient-to-br from-gray-100 via-white to-gray-200 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-300 rounded-2xl shadow-xl p-10 transition-all hover:shadow-2xl duration-300">
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold text-indigo-700 mb-1">
                        🎯 Assign a New Role
                    </h3>
                    <p class="text-sm text-gray-500">
                        Choose a role from the dropdown below to update permissions for <strong>{{ $user->name }}</strong>.
                    </p>
                </div>

                @if(session('success'))
                    <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-green-100 border-l-4 border-green-500 text-green-800 rounded-md shadow-sm animate-fade-in-down">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Role</label>
                        <select name="role"
                                class="w-full mt-1 block rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 transition ease-in-out duration-150">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @if($user->hasRole($role->name)) selected @endif>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium px-6 py-2 rounded-lg shadow-lg hover:from-indigo-700 hover:to-purple-700 transition transform hover:scale-105">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7" />
                            </svg>
                            Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

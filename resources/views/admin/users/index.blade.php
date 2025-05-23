<x-app-layout>
    <x-slot name="header">
        <div class="max-w-md bg-gradient-to-r from-teal-400 via-blue-500 to-purple-600 
                    rounded-3xl px-8 py-4 shadow-2xl cursor-default
                    transform transition duration-500 hover:scale-105 hover:shadow-[0_10px_25px_rgba(99,102,241,0.7)]">
            <h2 class="text-3xl font-extrabold text-white tracking-wide select-none
                       bg-clip-text text-transparent bg-gradient-to-r from-white to-white
                       animate-pulse text-left">
                Manage Users
            </h2>
        </div>
    </x-slot>

    <div class="pt-2 pb-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-2xl sm:rounded-2xl p-8 border border-gray-200">
                <h1 class="text-4xl font-extrabold text-indigo-600 mb-8 text-center drop-shadow-md">
                    Users List
                </h1>

                @if(session('success'))
                    <div class="mb-6 px-6 py-3 rounded bg-green-100 text-green-800 font-semibold text-center shadow-md">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300 shadow-md rounded-lg">
                        <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Role(s)</th>
                                <th scope="col" class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr class="hover:bg-indigo-50 transition duration-200 ease-in-out cursor-pointer">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-800 font-medium text-lg">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 text-md">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-700 font-semibold text-md">
                                        {{ $user->roles->pluck('name')->join(', ') ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg shadow-lg transition transform hover:scale-105">
                                            Edit Role
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

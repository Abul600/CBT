<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Paper Setters') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-4">Manage Paper Setters</h1>

                <!-- Show flash error if limit exceeded -->
                @if (session('error'))
                    <div class="bg-red-100 text-red-800 p-3 mb-4 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Active Paper Setter Count -->
                @php
                    $activeCount = $paperSetters->where('is_active', 1)->count();
                @endphp

                <p class="mb-2 text-gray-700 font-semibold">
                    Active Paper Setters: {{ $activeCount }} / 3
                </p>

                <!-- Always show Add button -->
                <a href="{{ route('moderator.paper_setters.create') }}" 
                   class="{{ $activeCount >= 3 ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600' }} text-white px-4 py-2 rounded inline-block">
                    Add Paper Setter
                </a>

                <!-- Paper Setters Table -->
                <table class="mt-4 w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">Name</th>
                            <th class="border border-gray-300 px-4 py-2">Email</th>
                            <th class="border border-gray-300 px-4 py-2">Status</th>
                            <th class="border border-gray-300 px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paperSetters as $setter)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $setter->name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $setter->email }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <span class="px-2 py-1 rounded text-white 
                                          {{ $setter->is_active ? 'bg-green-500' : 'bg-red-500' }}">
                                        {{ $setter->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 px-4 py-2 flex space-x-2">
                                    <!-- Edit Button -->
                                    <a href="{{ route('moderator.paper_setters.edit', $setter) }}" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                                        ✏️ Edit
                                    </a>

                                    <!-- Activate/Deactivate Button -->
                                    <form action="{{ route('moderator.paper_setters.toggleStatus', $setter->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="px-3 py-1 rounded text-white 
                                                {{ $setter->is_active ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
                                            {{ $setter->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>

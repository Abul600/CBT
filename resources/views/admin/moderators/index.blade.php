<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-500 p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Manage Moderators') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <!-- Add Moderator Button -->
                <a href="{{ route('admin.moderators.create') }}" 
                   class="px-4 py-2 bg-green-500 hover:bg-green-400 text-white rounded">
                   ➕ {{ __('Add Moderator') }}
                </a>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mt-3 p-3 bg-green-200 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Moderator Table -->
                <table class="table-auto w-full mt-4 border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2 border">Name</th>
                            <th class="px-4 py-2 border">Email</th>
                            <th class="px-4 py-2 border">Phone No</th>
                            <th class="px-4 py-2 border">Role</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($moderators as $moderator)
                            <tr class="hover:bg-gray-100">
                                <td class="border px-4 py-2">{{ $moderator->name }}</td>
                                <td class="border px-4 py-2">{{ $moderator->email }}</td>
                                <td class="border px-4 py-2">{{ $moderator->phone }}</td>
                                <td class="border px-4 py-2">{{ $moderator->getRoleNames()->first() ?? 'N/A' }}</td>
                                <td class="border px-4 py-2 flex space-x-2">
                                    
                                    <!-- Edit Button -->
                                    <a href="{{ route('admin.moderators.edit', $moderator->id) }}" 
                                       class="px-4 py-2 bg-yellow-500 text-white rounded">
                                        Edit
                                    </a>
                                    
                                    <!-- Activate/Deactivate Button -->
                                    @if($moderator->is_active)
                                        <a href="{{ route('moderator.deactivate', $moderator->id) }}" 
                                           class="px-4 py-2 bg-red-500 text-white rounded">
                                            Deactivate
                                        </a>
                                    @else
                                        <a href="{{ route('moderator.activate', $moderator->id) }}" 
                                           class="px-4 py-2 bg-green-500 text-white rounded">
                                            Activate
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center p-4">No moderators found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>

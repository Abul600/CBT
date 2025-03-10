<x-app-layout>
    <x-slot name="header">
    <div class="bg-blue-500 p-4 rounded-md">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Manage Moderators') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <a href="{{ route('admin.moderators.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded">Add Moderator</a>
                <table class="table-auto w-full mt-4">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($moderators as $moderator)
                            <tr>
                                <td class="border px-4 py-2">{{ $moderator->name }}</td>
                                <td class="border px-4 py-2">{{ $moderator->email }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('admin.moderators.edit', $moderator->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>
                                    <form action="{{ route('admin.moderators.destroy', $moderator->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded" onclick="return confirm('Are you sure?')">Delete</button>
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

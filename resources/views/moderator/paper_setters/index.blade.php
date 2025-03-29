<x-app-layout>
    <x-slot name="header">
    <div class="bg-blue-500 p-4 rounded-md">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Manage Paper Setters') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                

                <a href="{{ route('moderator.paper_setters.create') }}" 
                   class="bg-green-500 hover:bg-green-400 text-white px-4 py-2 rounded">
                    ➕ Add Paper Setter
                </a>

                <table class="mt-4 w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">Name</th>
                            <th class="border border-gray-300 px-4 py-2">Email</th>
                            <th class="border border-gray-300 px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paperSetters as $setter)
                            <tr>
                                <td class="border border-gray-300 px-4 py-2">{{ $setter->name }}</td>
                                <td class="border border-gray-300 px-4 py-2">{{ $setter->email }}</td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <form action="{{ route('moderator.paper_setters.destroy', $setter) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                            ❌ Delete
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

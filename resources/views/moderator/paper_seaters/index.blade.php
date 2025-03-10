<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Paper Seaters') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <a href="{{ route('moderator.paper_seaters.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded">Add Paper Seater</a>

                <table class="mt-4 w-full border">
                    <tr class="bg-gray-200">
                        <th class="p-2 border">Name</th>
                        <th class="p-2 border">Email</th>
                        <th class="p-2 border">Actions</th>
                    </tr>
                    @foreach ($paperSeaters as $paperSeater)
                        <tr>
                            <td class="p-2 border">{{ $paperSeater->name }}</td>
                            <td class="p-2 border">{{ $paperSeater->email }}</td>
                            <td class="p-2 border">
                                <form action="{{ route('moderator.paper_seaters.destroy', $paperSeater->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-3 py-1 rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

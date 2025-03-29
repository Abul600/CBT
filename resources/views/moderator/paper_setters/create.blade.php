<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Paper Setter') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('moderator.paper_setters.store') }}" method="POST">
                    @csrf
                    <div>
                        <label class="block font-medium text-sm text-gray-700">Name</label>
                        <input type="text" name="name" required class="border-gray-300 rounded w-full">
                    </div>
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Email</label>
                        <input type="email" name="email" required class="border-gray-300 rounded w-full">
                    </div>
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Phone</label>
                        <input type="text" name="phone" required class="border-gray-300 rounded w-full">
                    </div>
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">District</label>
                        <input type="text" name="district" value="{{ auth()->user()->district }}" readonly class="border-gray-300 rounded w-full bg-gray-100 cursor-not-allowed">
                    </div>
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Password</label>
                        <input type="password" name="password" required class="border-gray-300 rounded w-full">
                    </div>
                    <div class="mt-4">
                        <label class="block font-medium text-sm text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="border-gray-300 rounded w-full">
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            Add Paper Setter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

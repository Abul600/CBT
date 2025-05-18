<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Exam') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">

                @if ($errors->any())
                    <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('moderator.exams.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block font-semibold">Exam Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border-gray-300 rounded p-2" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block font-semibold">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full border-gray-300 rounded p-2">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="duration" class="block font-semibold">Duration (minutes)</label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration') }}" class="w-full border-gray-300 rounded p-2" min="1">
                    </div>

                    <div class="mb-4">
                        <label for="start_time" class="block font-semibold">Start Time</label>
                        <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" class="w-full border-gray-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label for="end_time" class="block font-semibold">End Time</label>
                        <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" class="w-full border-gray-300 rounded p-2">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="district_id">
                            District (Optional)
                        </label>
                        <select name="district_id" id="district_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                            <option value="">-- No District --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Create Exam
                    </button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>

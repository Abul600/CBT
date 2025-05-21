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

                    <!-- Application Start -->
                    <div class="mb-4">
                        <label for="application_start" class="block font-semibold">Application Start Date</label>
                        <input type="datetime-local" name="application_start" id="application_start"
                               value="{{ old('application_start') }}"
                               class="w-full border-gray-300 rounded p-2" required>
                    </div>

                    <!-- Application End -->
                    <div class="mb-4">
                        <label for="application_end" class="block font-semibold">Application End Date</label>
                        <input type="datetime-local" name="application_end" id="application_end"
                               value="{{ old('application_end') }}"
                               class="w-full border-gray-300 rounded p-2" required>
                    </div>

                    <!-- Exam Start -->
                    <div class="mb-4">
                        <label for="exam_start" class="block font-semibold">Exam Date & Time</label>
                        <input type="datetime-local" name="exam_start" id="exam_start"
                               value="{{ old('exam_start') }}"
                               class="w-full border-gray-300 rounded p-2" required>
                        <small class="text-gray-500">Students can join 10 minutes before this time</small>
                    </div>

                    <!-- Exam Duration -->
                    <div class="mb-4">
                        <label for="duration" class="block font-semibold">Exam Duration (Minutes)</label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration') }}"
                               class="w-full border-gray-300 rounded p-2" min="1" required>
                    </div>

                    <!-- District Selection -->
                    <div class="mb-4">
                        <label for="district_id" class="block font-semibold">District (Optional)</label>
                        <select name="district_id" id="district_id"
                                class="w-full border-gray-300 rounded p-2">
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

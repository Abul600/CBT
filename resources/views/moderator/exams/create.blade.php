<x-app-layout>
   <x-slot name="header">
    <div class="inline-block px-4 py-2 bg-gradient-to-r from-fuchsia-500 via-orange-400 to-purple-600 rounded-3xl shadow-lg">
        <h2 class="text-2xl md:text-3xl font-extrabold text-white flex items-center space-x-2 md:space-x-3">
            <span>🌟</span>
            <span>Create Exam</span>
        </h2>
    </div>
</x-slot>


    <div class="py-12 bg-gradient-to-tr from-purple-300 via-pink-300 to-yellow-200 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-12 rounded-3xl shadow-2xl border border-gray-200
                        bg-gradient-to-br from-white via-indigo-50 to-pink-50
                        backdrop-blur-lg transition
                        hover:shadow-[0_12px_48px_rgba(219,39,119,0.25)]
                        hover:ring-4 hover:ring-pink-400/40">

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-300 text-red-700 px-5 py-4 rounded-xl mb-6 shadow">
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>⚠️ {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('moderator.exams.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- Exam Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-900 mb-1">Exam Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition"
                            required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-900 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition"
                            placeholder="Write a short description">{{ old('description') }}</textarea>
                    </div>

                    <!-- Exam Type -->
                    <div>
                        <label for="examType" class="block text-sm font-bold text-gray-900 mb-1">Exam Type</label>
                        <select name="type" id="examType"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition"
                            required>
                            <option value="scheduled" {{ old('type') == 'scheduled' ? 'selected' : '' }}>📅 Scheduled</option>
                            <option value="mock" {{ old('type') == 'mock' ? 'selected' : '' }}>📝 Mock Test</option>
                        </select>
                    </div>

                    <!-- Scheduled Fields -->
                    <div id="scheduledFields" class="space-y-6 hidden">
                        <div>
                            <label for="application_start" class="block text-sm font-bold text-gray-900 mb-1">Application Start</label>
                            <input type="datetime-local" name="application_start" id="application_start"
                                   value="{{ old('application_start') }}"
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition">
                        </div>

                        <div>
                            <label for="application_end" class="block text-sm font-bold text-gray-900 mb-1">Application End</label>
                            <input type="datetime-local" name="application_end" id="application_end"
                                   value="{{ old('application_end') }}"
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition">
                        </div>

                        <div>
                            <label for="exam_start" class="block text-sm font-bold text-gray-900 mb-1">Exam Start</label>
                            <input type="datetime-local" name="exam_start" id="exam_start"
                                   value="{{ old('exam_start') }}"
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition">
                        </div>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration" class="block text-sm font-bold text-gray-900 mb-1">Duration (Minutes)</label>
                        <input type="number" id="duration" name="duration" value="{{ old('duration') }}"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition"
                            min="1" required>
                    </div>

                    <!-- District -->
                    <div>
                        <label for="district_id" class="block text-sm font-bold text-gray-900 mb-1">District (Optional)</label>
                        <select name="district_id" id="district_id"
                            class="w-full px-4 py-2 border-2 border-gray-300 rounded-md bg-white shadow-inner text-gray-800 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:border-indigo-600 transition">
                            <option value="">-- No District --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Submit -->
                    <div class="pt-6">
                        <button type="submit"
                            class="w-full md:w-auto bg-gradient-to-r from-pink-500 via-red-500 to-yellow-500 hover:from-pink-600 hover:to-yellow-600 text-white font-bold px-6 py-3 rounded-lg shadow-lg transition transform hover:scale-105 focus:ring-4 focus:ring-yellow-300">
                            🚀 Create Exam
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const examType = document.getElementById('examType');
            const scheduledFields = document.getElementById('scheduledFields');

            const toggleFields = () => {
                scheduledFields.classList.toggle('hidden', examType.value !== 'scheduled');
            };

            examType.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</x-app-layout>

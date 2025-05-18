<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Moderator: Review Questions
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Exam Selection --}}
            <div class="mb-6 bg-white p-6 rounded-lg shadow-sm">
                <label for="exam_id" class="block text-sm font-medium text-gray-700">
                    View Questions Assigned to Exam:
                </label>
                <select name="exam_id" id="exam_id"
                        class="block w-full mt-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200"
                        onchange="handleExamSelection(this)">
                    <option value="">-- Select Exam --</option>
                    @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ optional($selectedExam)->id == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Unassigned Questions --}}
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Unassigned Questions</h3>
                    <span class="text-sm text-gray-500">
                        {{ $availableQuestions->count() }} available questions
                    </span>
                </div>

                @if($availableQuestions->isEmpty())
                    <p class="text-gray-500">No pending questions available.</p>
                @else
                    <form method="POST"
                          action="{{ route('moderator.exams.questions.assign', ['exam' => $selectedExam->id ?? '']) }}"
                          id="assign-questions-form">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="px-6 py-3">
                                            <input type="checkbox" onclick="toggleAll(this)">
                                        </th>
                                        <th class="px-6 py-3">Question</th>
                                        <th class="px-6 py-3">Paper Setter</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($availableQuestions as $question)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <input type="checkbox" name="question_ids[]" value="{{ $question->id }}"
                                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200">
                                            </td>
                                            <td class="px-6 py-4">{{ Str::limit($question->text, 100) }}</td>
                                            <td class="px-6 py-4">{{ $question->paperSetter->name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex justify-end space-x-4">
                            <button type="button" onclick="toggleSelection()"
                                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                Toggle Select All
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 text-white font-semibold text-xs uppercase rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                Assign Selected Questions
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Assigned Questions --}}
            @if($selectedExam)
                <div class="bg-white p-6 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Assigned to: {{ $selectedExam->name }}</h3>
                        <span class="text-sm text-gray-500">
                            {{ $selectedExam->questions->count() }} assigned questions
                        </span>
                    </div>

                    @if($selectedExam->questions->isEmpty())
                        <p class="text-gray-500">No questions assigned yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead>
                                    <tr class="bg-gray-100 text-left">
                                        <th class="px-6 py-3">Question</th>
                                        <th class="px-6 py-3">Unassign</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($selectedExam->questions as $question)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">{{ Str::limit($question->text, 100) }}</td>
                                            <td class="px-6 py-4">
                                                <form method="POST"
                                                      action="{{ route('moderator.exams.unassign_question', ['exam' => $selectedExam->id, 'question' => $question->id]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-900"
                                                            onclick="return confirm('Unassign this question?')">
                                                        Unassign
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        function handleExamSelection(select) {
            const examId = select.value;
            const url = examId ? `/moderator/exams/${examId}/questions/view` : `/moderator/exams/view-questions`;
            window.location.href = url;
        }

        function toggleSelection() {
            const checkboxes = document.querySelectorAll('input[name="question_ids[]"]');
            const allChecked = [...checkboxes].every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
        }

        function toggleAll(source) {
            const checkboxes = document.querySelectorAll('input[name="question_ids[]"]');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        document.getElementById('assign-questions-form')?.addEventListener('submit', function(e) {
            const selected = document.querySelectorAll('input[name="question_ids[]"]:checked');
            if (selected.length === 0) {
                e.preventDefault();
                alert('Please select at least one question to assign!');
            }
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Moderator: Review Questions
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- Exam Selection --}}
            <form method="GET" action="" id="examSelectForm" class="mb-4">
                <div class="form-group">
                    <label for="exam_id">View Questions Assigned to Exam:</label>
                    <select name="exam_id" id="exam_id" class="form-control">
                        <option value="">-- Select Exam --</option>
                        @foreach($exams as $ex)
                            <option value="{{ $ex->id }}" {{ $selectedExamId == $ex->id ? 'selected' : '' }}>
                                {{ $ex->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <script>
                document.getElementById('exam_id').addEventListener('change', function () {
                    const selectedExamId = this.value;
                    if (selectedExamId) {
                        window.location.href = `/moderator/exams/${selectedExamId}/questions`;
                    } else {
                        window.location.href = `/moderator/exams/questions/view`;
                    }
                });
            </script>

            {{-- Unassigned Questions --}}
            <div class="mt-4">
                <h4>Unassigned Questions</h4>

                @if($questions->isEmpty())
                    <p>No pending questions available.</p>
                @else
                    <form method="POST" action="{{ route('moderator.exams.assign_questions', ['exam' => $selectedExamId]) }}">
                        @csrf
                        <div class="form-group">
                            <label for="exam_id_assign">Assign To Exam:</label>
                            <select name="exam_id" id="exam_id_assign" class="form-control" required>
                                <option value="">-- Select Exam --</option>
                                @foreach($exams as $ex)
                                    <option value="{{ $ex->id }}">{{ $ex->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Question</th>
                                    <th>Options</th>
                                    <th>Correct</th>
                                    <th>From</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($questions as $question)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="question_ids[]" value="{{ $question->id }}">
                                        </td>
                                        <td>{{ $question->content }}</td>
                                        <td>
                                            A: {{ $question->option_a }}<br>
                                            B: {{ $question->option_b }}<br>
                                            C: {{ $question->option_c }}<br>
                                            D: {{ $question->option_d }}
                                        </td>
                                        <td>{{ $question->correct }}</td>
                                        <td>{{ $question->paperSetter->name ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary mt-2">Assign Selected Questions</button>
                    </form>
                @endif
            </div>

            {{-- Assigned Questions --}}
            @if($selectedExamId && isset($exams->firstWhere('id', $selectedExamId)->questions))
                @php $selectedExam = $exams->firstWhere('id', $selectedExamId); @endphp
                <hr class="my-4">
                <h4>Questions Already Assigned to: {{ $selectedExam->name }}</h4>

                @if($selectedExam->questions->isEmpty())
                    <p>No questions assigned to this exam yet.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Content</th>
                                <th>Correct</th>
                                <th>From</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($selectedExam->questions as $question)
                                <tr>
                                    <td>{{ $question->content }}</td>
                                    <td>{{ $question->correct }}</td>
                                    <td>{{ $question->paperSetter->name ?? 'N/A' }}</td>
                                    <td>
                                        {{-- Fixed unassign route with both parameters --}}
                                        <form action="{{ route('moderator.exams.unassign_question', ['exam' => $selectedExamId, 'question' => $question->id]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Unassign this question?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger">Unassign</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
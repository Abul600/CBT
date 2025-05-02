@extends('layouts.moderator')

@section('content')
<div class="container mt-4">
    <h2>Questions Sent To You</h2>

    @if($questions->isEmpty())
        <p class="text-muted">No questions sent to you yet.</p>
    @else
        <form method="POST" action="{{ route('moderator.assign.questions') }}">
            @csrf

            {{-- Dropdown to select exam --}}
            <div class="form-group mb-3">
                <label for="exam_id">Select Exam</label>
                <select name="exam_id" id="exam_id" class="form-control" onchange="this.form.submit()" required>
                    <option value="">-- Select Exam --</option>
                    @foreach ($exams as $examItem)
                        <option value="{{ $examItem->id }}" {{ (isset($exam) && $exam->id == $examItem->id) ? 'selected' : '' }}>
                            {{ $examItem->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Table of questions sent to the moderator --}}
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Question</th>
                        <th>Type</th>
                        <th>Marks</th>
                        <th>Status</th>
                        <th>Paper Setter</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $question)
                        @if ($question->sent_to_moderator_id == Auth::id() && is_null($question->exam_id))
                        <tr>
                            <td>
                                <input type="checkbox" name="question_ids[]" value="{{ $question->id }}">
                            </td>
                            <td>{{ $question->question_text }}</td>
                            <td>{{ ucfirst($question->type) }}</td>
                            <td>{{ $question->marks }}</td>
                            <td>{{ ucfirst($question->status) }}</td>
                            <td>{{ $question->paperSetter?->name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-success mt-3">Assign Selected Questions</button>
        </form>
    @endif

    {{-- Display assigned questions for the selected exam --}}
    @if (isset($exam))
        <hr class="my-5">
        <h2>Questions for Exam: {{ $exam->name }}</h2>

        @if ($exam->questions->isEmpty())
            <p class="text-muted">No questions assigned yet.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Question Text</th>
                        <th>Type</th>
                        <th>Marks</th>
                        <th>Status</th>
                        <th>Paper Setter</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($exam->questions as $question)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $question->question_text }}</td>
                        <td>{{ ucfirst($question->type) }}</td>
                        <td>{{ $question->marks }}</td>
                        <td>{{ ucfirst($question->status) }}</td>
                        <td>{{ $question->paperSetter?->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</div>
@endsection

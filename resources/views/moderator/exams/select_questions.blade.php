@extends('layouts.moderator')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Manage Questions for Exam: {{ $exam->name }}</h2>

    @unless($exam->is_released)
        {{-- Assign Questions Form --}}
        <form method="POST" action="{{ route('moderator.exams.assign-questions', $exam) }}">
            @csrf

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Available Questions (Unassigned)</h5>
                </div>
                <div class="card-body">
                    @if ($unassignedQuestions->isEmpty())
                        <p class="text-muted mb-0">No unassigned questions available.</p>
                    @else
                        <div class="table-responsive mb-3">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="select-all-unassigned">
                                        </th>
                                        <th>Question</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unassignedQuestions as $question)
                                        <tr>
                                            <td>
                                                <input 
                                                    type="checkbox" 
                                                    name="question_ids[]" 
                                                    value="{{ $question->id }}"
                                                    {{-- Disable checkbox if question type is descriptive and exam is mock --}}
                                                    @if($exam->type === 'mock' && $question->type === 'descriptive') disabled @endif
                                                >
                                            </td>
                                            <td>{{ Str::limit($question->text, 80) }}</td>
                                            <td class="text-center text-capitalize">{{ $question->type }}</td>
                                            <td class="text-center">{{ $question->marks }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary" 
                            {{-- Disable submit button if exam is mock and all unassigned questions are descriptive --}}
                            @if($exam->type === 'mock' && $unassignedQuestions->every(fn($q) => $q->type === 'descriptive')) disabled @endif
                        >
                            <i class="fas fa-plus me-2"></i>Assign Selected
                        </button>
                    @endif
                </div>
            </div>
        </form>

        {{-- Unassign Questions Form --}}
        <form method="POST" action="{{ route('moderator.exams.unassign-questions', $exam) }}">
            @csrf

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Currently Assigned Questions</h5>
                </div>
                <div class="card-body">
                    @if ($assignedQuestions->isEmpty())
                        <p class="text-muted mb-0">No questions assigned to this exam.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">
                                            <input type="checkbox" id="select-all-assigned">
                                        </th>
                                        <th>Question</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assignedQuestions as $question)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="question_ids[]" value="{{ $question->id }}">
                                            </td>
                                            <td>{{ Str::limit($question->text, 80) }}</td>
                                            <td class="text-center text-capitalize">{{ $question->type }}</td>
                                            <td class="text-center">{{ $question->marks }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-minus me-2"></i>Unassign Selected
                        </button>
                    @endif
                </div>
            </div>
        </form>
    @else
        {{-- If exam is released --}}
        <div class="alert alert-warning">
            This exam has already been released. You cannot modify its questions.
        </div>
    @endunless

    {{-- Back Link --}}
    <div class="d-flex justify-content-end mt-3">
        <a href="{{ route('moderator.exams.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Exams
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select All for Unassigned Questions
        document.getElementById('select-all-unassigned')?.addEventListener('change', function (e) {
            document.querySelectorAll('form[action*="assign-questions"] input[name="question_ids[]"]').forEach(cb => {
                // Only check enabled checkboxes (not disabled)
                if (!cb.disabled) {
                    cb.checked = e.target.checked;
                }
            });
        });

        // Select All for Assigned Questions
        document.getElementById('select-all-assigned')?.addEventListener('change', function (e) {
            document.querySelectorAll('form[action*="unassign-questions"] input[name="question_ids[]"]').forEach(cb => {
                cb.checked = e.target.checked;
            });
        });
    });
</script>
@endsection


{{-- Additional: Question Type Select Dropdown (for creating/editing question) with frontend validation --}}
{{-- This snippet you can embed where needed in your question creation form --}}

{{-- Example snippet: --}}
{{-- 
<select name="type" class="form-select" 
        {{ $exam->type === 'mock' ? 'disabled' : '' }}>
    <option value="mcq1" {{ old('type') == 'mcq1' ? 'selected' : '' }}>MCQ Type 1</option>
    <option value="mcq2" {{ old('type') == 'mcq2' ? 'selected' : '' }}>MCQ Type 2</option>
    <option value="descriptive" 
            {{ $exam->type === 'mock' ? 'disabled' : '' }}
            {{ old('type') == 'descriptive' ? 'selected' : '' }}>
        Descriptive
    </option>
</select>

@if($exam->type === 'mock')
<div class="text-sm text-danger mt-1">
    Descriptive questions are disabled for mock exams
</div>
@endif 
--}}

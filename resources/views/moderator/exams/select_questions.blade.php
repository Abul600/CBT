@extends('layouts.moderator')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Manage Questions for Exam: {{ $exam->name }}</h2>

    <form method="POST" action="{{ route('moderator.exams.sync_questions', $exam->id) }}">
        @csrf

        {{-- Unassigned Questions --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Available Questions (Unassigned)</h5>
            </div>
            <div class="card-body">
                @if ($unassignedQuestions->isEmpty())
                    <p class="text-muted mb-0">No unassigned questions available.</p>
                @else
                    <div class="table-responsive">
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
                                            <input type="checkbox" name="assign_ids[]" value="{{ $question->id }}">
                                        </td>
                                        <td>{{ Str::limit($question->text, 80) }}</td>
                                        <td class="text-center text-capitalize">{{ $question->type }}</td>
                                        <td class="text-center">{{ $question->marks }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Assigned Questions --}}
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
                                            <input type="checkbox" name="unassign_ids[]" value="{{ $question->id }}">
                                        </td>
                                        <td>{{ Str::limit($question->text, 80) }}</td>
                                        <td class="text-center text-capitalize">{{ $question->type }}</td>
                                        <td class="text-center">{{ $question->marks }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Save Changes
            </button>
            <a href="{{ route('moderator.exams.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Exams
            </a>
        </div>
    </form>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All Unassigned
        document.getElementById('select-all-unassigned')?.addEventListener('change', function(e) {
            document.querySelectorAll('input[name="assign_ids[]"]').forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });

        // Select All Assigned
        document.getElementById('select-all-assigned')?.addEventListener('change', function(e) {
            document.querySelectorAll('input[name="unassign_ids[]"]').forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });
    });
</script>
@endsection
@endsection
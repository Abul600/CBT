@extends('layouts.moderator')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manage Exams</h2>
        <div class="d-flex gap-2">
            @if(Route::has('moderator.exams.view.questions'))
                <a href="{{ route('moderator.exams.view.questions') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-inbox"></i> View Received Questions
                </a>
            @endif
            <a href="{{ route('moderator.exams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Exam
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($exams->isEmpty()))
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                <p class="mb-0">No exams available. Create your first exam!</p>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Exam Name</th>
                                <th scope="col">Assigned Questions</th>
                                <th scope="col">Pending Questions</th>
                                <th scope="col" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                <tr>
                                    <td>{{ $exam->name }}</td>
                                    <td>{{ $exam->questions()->count() }}</td>
                                    <td>
                                        @php
                                            $pendingCount = $exam->questions()
                                                ->where('status', 'pending')
                                                ->count();
                                        @endphp
                                        @if($pendingCount > 0)
                                            <span class="badge bg-warning text-dark">
                                                {{ $pendingCount }} pending review
                                            </span>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="{{ route('moderator.exams.questions', $exam->id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               data-bs-toggle="tooltip"
                                               title="View assigned questions">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('moderator.exams.select_questions', $exam) }}" 
                                               class="btn btn-sm btn-outline-success"
                                               data-bs-toggle="tooltip" 
                                               title="Add questions to exam">
                                                <i class="fas fa-question-circle"></i>
                                            </a>

                                            @if($pendingCount > 0)
                                                <a href="{{ route('moderator.review.questions.index', ['exam_id' => $exam->id]) }}" 
                                                   class="btn btn-sm btn-outline-warning"
                                                   data-bs-toggle="tooltip"
                                                   title="Review pending questions">
                                                    <i class="fas fa-check-double"></i>
                                                </a>
                                            @endif

                                            <form action="{{ route('moderator.exams.destroy', $exam) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete exam"
                                                        onclick="return confirm('Are you sure? This will permanently delete the exam.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Bootstrap tooltips
        const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    });
</script>
@endsection
@extends('layouts.moderator')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="display-5 fw-bold text-gradient-purple">
            <i class="fas fa-book-open me-2"></i> Manage Exams
        </h2>
        <div class="d-flex gap-3">
            @if(Route::has('moderator.exams.view.questions'))
                <a href="{{ route('moderator.exams.view.questions') }}" 
                   class="btn btn-outline-gradient-info shadow-sm fw-semibold px-4"
                   title="View Received Questions">
                    <i class="fas fa-inbox me-2"></i> View Received Questions
                </a>
            @endif
            <a href="{{ route('moderator.exams.create') }}" 
               class="btn btn-gradient-primary shadow fw-semibold px-4"
               title="Add New Exam">
                <i class="fas fa-plus me-2"></i> Add New Exam
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow rounded-3 fs-5" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($exams->isEmpty())
        <div class="card shadow-sm border-0 rounded-4 py-5">
            <div class="card-body text-center text-muted">
                <i class="fas fa-clipboard-list fa-4x mb-4 text-gradient-purple"></i>
                <p class="fs-5 fw-semibold">No exams available. Create your first exam!</p>
            </div>
        </div>
    @else
        <div class="card shadow-lg rounded-4 border-gradient-purple border-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle text-center">
                        <thead class="table-gradient-purple text-white fs-6 fw-semibold">
                            <tr>
                                <th scope="col" class="text-start ps-4">Exam Name</th>
                                <th scope="col">Assigned Questions</th>
                                <th scope="col">Pending Questions</th>
                                <th scope="col">Release</th>
                                <th scope="col" class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                                @php
                                    $assignedCount = $exam->questions()->count();
                                    $pendingCount = $exam->questions()->where('status', 'pending')->count();
                                @endphp
                                <tr class="align-middle" style="transition: background-color 0.3s ease;" 
                                    onmouseenter="this.style.backgroundColor='#f3e8ff';" 
                                    onmouseleave="this.style.backgroundColor='';">
                                    <td class="text-start ps-4 fs-5 fw-semibold text-purple-800">{{ $exam->name }}</td>

                                    <td>
                                        <span class="badge bg-gradient-info px-3 py-2 fs-6">
                                            {{ $assignedCount }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($pendingCount > 0)
                                            <span class="badge bg-gradient-warning text-dark px-3 py-2 fs-6">
                                                {{ $pendingCount }} pending review
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">None</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($exam->is_released)
                                            <span class="badge bg-gradient-success px-3 py-2 fs-6">Released</span><br>
                                            <small class="text-muted fst-italic">{{ $exam->released_at->diffForHumans() }}</small>
                                        @else
                                            <form action="{{ route('moderator.exams.release', $exam) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-gradient-primary btn-sm shadow fw-semibold"
                                                    {{ $assignedCount < 1 ? 'disabled' : '' }}>
                                                    Release to Students
                                                </button>
                                                @if($assignedCount < 1)
                                                    <small class="text-danger d-block mt-1">Add questions first</small>
                                                @endif
                                            </form>
                                        @endif
                                    </td>

                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end flex-wrap">
                                            <a href="{{ route('moderator.exams.questions', $exam->id) }}" 
                                               class="btn btn-gradient-info btn-sm shadow"
                                               data-bs-toggle="tooltip"
                                               title="View assigned questions">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('moderator.exams.select_questions', $exam) }}" 
                                               class="btn btn-outline-gradient-success btn-sm shadow"
                                               data-bs-toggle="tooltip" 
                                               title="Add questions to exam">
                                                <i class="fas fa-question-circle"></i>
                                            </a>

                                            @if($pendingCount > 0)
                                                <a href="{{ route('moderator.review.questions.index', ['exam_id' => $exam->id]) }}" 
                                                   class="btn btn-outline-gradient-warning btn-sm shadow"
                                                   data-bs-toggle="tooltip"
                                                   title="Review pending questions">
                                                    <i class="fas fa-check-double"></i>
                                                </a>
                                            @endif

                                            <form action="{{ route('moderator.exams.destroy', $exam) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-gradient-danger btn-sm shadow"
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

<style>
    /* Gradient text */
    .text-gradient-purple {
        background: linear-gradient(45deg, #7b2ff7, #f107a3);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Gradient table header */
    .table-gradient-purple {
        background: linear-gradient(90deg, #6a11cb, #2575fc);
    }

    /* Gradient border */
    .border-gradient-purple {
        border-image: linear-gradient(45deg, #7b2ff7, #f107a3) 1;
    }

    /* Gradient backgrounds for buttons and badges */
    .bg-gradient-primary {
        background: linear-gradient(45deg, #3b82f6, #9333ea);
        color: white !important;
        border: none;
        transition: background 0.3s ease;
    }
    .bg-gradient-primary:hover {
        background: linear-gradient(45deg, #2563eb, #7e22ce);
        color: white !important;
    }

    .btn-gradient-primary {
        background: linear-gradient(45deg, #3b82f6, #9333ea);
        color: white !important;
        border: none;
        transition: background 0.3s ease, transform 0.2s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.5);
    }
    .btn-gradient-primary:hover {
        background: linear-gradient(45deg, #2563eb, #7e22ce);
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.7);
        color: white !important;
    }

    .bg-gradient-info {
        background: linear-gradient(45deg, #0ea5e9, #38bdf8);
        color: white !important;
    }

    .btn-gradient-info {
        background: linear-gradient(45deg, #0ea5e9, #38bdf8);
        color: white !important;
        border: none;
        transition: background 0.3s ease, transform 0.2s ease;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.5);
    }
    .btn-gradient-info:hover {
        background: linear-gradient(45deg, #0284c7, #0ea5e9);
        transform: scale(1.05);
        box-shadow: 0 6px 18px rgba(14, 165, 233, 0.7);
        color: white !important;
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #16a34a, #4ade80);
        color: white !important;
    }

    .btn-outline-gradient-success {
        color: #16a34a;
        border: 2px solid #16a34a;
        transition: background 0.3s ease, color 0.3s ease;
    }
    .btn-outline-gradient-success:hover {
        background: linear-gradient(45deg, #16a34a, #4ade80);
        color: white !important;
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #facc15, #eab308);
        color: #212529 !important;
    }

    .btn-outline-gradient-warning {
        color: #b45309;
        border: 2px solid #b45309;
        transition: background 0.3s ease, color 0.3s ease;
    }
    .btn-outline-gradient-warning:hover {
        background: linear-gradient(45deg, #facc15, #eab308);
        color: #212529 !important;
    }

    .btn-outline-gradient-danger {
        color: #dc2626;
        border: 2px solid #dc2626;
        transition: background 0.3s ease, color 0.3s ease;
    }
    .btn-outline-gradient-danger:hover {
        background: linear-gradient(45deg, #dc2626, #f87171);
        color: white !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.forEach(tooltip => new bootstrap.Tooltip(tooltip));
    });
</script>
@endsection

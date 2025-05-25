@extends('layouts.moderator')

@section('content')
<div class="container py-5">
    <div class="mb-5 text-center">
        <h2 class="display-5 fw-bold bg-gradient-to-r text-white px-4 py-3 rounded-4"
            style="background: linear-gradient(90deg, #8e2de2, #4a00e0); 
                   box-shadow: 0 0 15px #8e2de2;">
             Questions for Exam: <span class="text-warning">{{ $exam->name }}</span> 
        </h2>
    </div>

    {{-- Success/Error Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-lg rounded-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-lg rounded-3" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($exam->questions->isEmpty())
        <div class="alert alert-info shadow-lg rounded-3 fs-5 text-center py-3">
            <i class="fas fa-info-circle me-2"></i> No questions submitted for this exam yet.
        </div>
    @else
        <div class="table-responsive shadow rounded-4 border border-3 border-purple-400 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr class="text-center text-white" style="background: linear-gradient(90deg, #7928ca, #ff0080);">
                        <th scope="col">#</th>
                        <th scope="col">Question</th>
                        <th scope="col">Type</th>
                        <th scope="col">Marks</th>
                        <th scope="col">Submitted By</th>
                        <th scope="col">Status</th>
                        <th scope="col" style="min-width: 220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->questions as $question)
                        <tr class="text-center align-middle" 
                            style="transition: background-color 0.3s ease;"
                            onmouseover="this.style.backgroundColor='#f3e8ff';" 
                            onmouseout="this.style.backgroundColor='';">
                            <th scope="row" class="fw-bold text-purple-600">{{ $loop->iteration }}</th>
                            <td class="text-start" style="max-width: 350px;">
                                {{ Str::limit($question->question_text, 80) }}
                            </td>
                            <td>
                                <span class="badge rounded-pill text-white 
                                    {{ $question->type === 'multiple' ? 'bg-primary' : 'bg-info' }} px-3 py-2 text-uppercase"
                                    style="font-weight: 600;">
                                    {{ $question->type }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-danger">{{ $question->marks }}</span>
                            </td>
                            <td>
                                <span class="fst-italic text-secondary">{{ $question->paperSetter->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @switch($question->status)
                                    @case('sent')
                                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2"
                                              style="font-weight: 600; text-transform: uppercase;">
                                            Pending Review
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="badge rounded-pill bg-success px-3 py-2"
                                              style="font-weight: 600; text-transform: uppercase;">
                                            Approved
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="badge rounded-pill bg-danger px-3 py-2"
                                              style="font-weight: 600; text-transform: uppercase;">
                                            Rejected
                                        </span>
                                        @break
                                    @default
                                        <span class="badge rounded-pill bg-secondary px-3 py-2"
                                              style="font-weight: 600; text-transform: uppercase;">
                                            {{ ucfirst($question->status) }}
                                        </span>
                                @endswitch
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    {{-- Approve Button --}}
                                    <form 
                                        action="{{ route('moderator.exams.questions.approve', [$exam->id, $question->id]) }}" 
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-gradient-success fw-bold"
                                            @if($question->status !== 'sent') disabled @endif
                                            title="Approve question"
                                            style="transition: transform 0.2s ease;"
                                            onmouseenter="this.style.transform='scale(1.1)';"
                                            onmouseleave="this.style.transform='scale(1)';"
                                        >
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>

                                    <!-- {{-- Reject Button --}}
                                    <form 
                                        action="{{ route('moderator.exams.questions.reject', [$exam->id, $question->id]) }}" 
                                        method="POST"
                                        class="d-inline"
                                    >
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-gradient-danger fw-bold"
                                            @if($question->status !== 'sent') disabled @endif
                                            title="Reject question"
                                            style="transition: transform 0.2s ease;"
                                            onmouseenter="this.style.transform='scale(1.1)';"
                                            onmouseleave="this.style.transform='scale(1)';"
                                        >
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form> -->

                                    {{-- View Details Button --}}
                                    <a 
                                        href="{{ route('moderator.exams.questions.show', $question->id) }}" 
                                        class="btn btn-sm btn-gradient-info fw-bold"
                                        title="View details"
                                        style="transition: transform 0.2s ease;"
                                        onmouseenter="this.style.transform='scale(1.1)';"
                                        onmouseleave="this.style.transform='scale(1)';"
                                    >
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    <!-- {{-- Unassign Button --}}
                                    <form 
                                        action="{{ route('moderator.questions.unassign', $question->id) }}" 
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to unassign this question?');"
                                        class="d-inline"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-outline-warning fw-bold"
                                            title="Unassign question"
                                            style="transition: background-color 0.3s ease;"
                                            onmouseenter="this.style.backgroundColor='#ffc107';"
                                            onmouseleave="this.style.backgroundColor='';"
                                        >
                                            <i class="fas fa-unlink me-1"></i>Unassign
                                        </button>
                                    </form> -->
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-5 text-center">
        <a 
            href="{{ route('moderator.exams.index') }}" 
            class="btn btn-lg btn-outline-secondary fw-semibold"
            style="box-shadow: 0 0 10px rgba(0,0,0,0.1); transition: box-shadow 0.3s ease;"
            onmouseenter="this.style.boxShadow='0 0 20px #6c757d';"
            onmouseleave="this.style.boxShadow='0 0 10px rgba(0,0,0,0.1)';"
        >
            <i class="fas fa-arrow-left me-2"></i>Back to Exams
        </a>
    </div>
</div>

{{-- Custom Gradient Button Styles --}}
<style>
    .btn-gradient-success {
        background: linear-gradient(45deg, #28a745, #85e085);
        color: white;
        border: none;
    }
    .btn-gradient-success:hover:not(:disabled) {
        background: linear-gradient(45deg, #1e7e34, #4caf50);
        color: white;
    }
    .btn-gradient-danger {
        background: linear-gradient(45deg, #dc3545, #f37a7a);
        color: white;
        border: none;
    }
    .btn-gradient-danger:hover:not(:disabled) {
        background: linear-gradient(45deg, #a71d2a, #d9534f);
        color: white;
    }
    .btn-gradient-info {
        background: linear-gradient(45deg, #17a2b8, #6cd3de);
        color: white;
        border: none;
    }
    .btn-gradient-info:hover {
        background: linear-gradient(45deg, #117a8b, #2d9ca8);
        color: white;
    }
</style>
@endsection

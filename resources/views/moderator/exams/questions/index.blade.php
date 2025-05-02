@extends('layouts.moderator')

@section('content')
<div class="container">
    <h2 class="mb-4">Questions for Exam: {{ $exam->name }}</h2>

    {{-- Success/Error Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($exam->questions->isEmpty())
        <div class="alert alert-info">
            No questions submitted for this exam yet.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Question</th>
                        <th scope="col">Type</th>
                        <th scope="col">Marks</th>
                        <th scope="col">Submitted By</th>
                        <th scope="col">Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->questions as $question)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ Str::limit($question->question_text, 60) }}</td>
                            <td>
                                <span class="text-capitalize">
                                    {{ $question->type }}
                                </span>
                            </td>
                            <td>{{ $question->marks }}</td>
                            <td>{{ $question->paperSetter->name ?? 'N/A' }}</td>
                            <td>
                                @switch($question->status)
                                    @case('sent')
                                        <span class="badge bg-warning text-dark">Pending Review</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-success">Approved</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($question->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    {{-- Approve Button --}}
                                    <form 
                                        action="{{ route('moderator.exams.questions.approve', [$exam->id, $question->id]) }}" 
                                        method="POST"
                                    >
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-success"
                                            @if($question->status !== 'sent') disabled @endif
                                            title="Approve question"
                                        >
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    {{-- Reject Button --}}
                                    <form 
                                        action="{{ route('moderator.exams.questions.reject', [$exam->id, $question->id]) }}" 
                                        method="POST"
                                    >
                                        @csrf
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-danger"
                                            @if($question->status !== 'sent') disabled @endif
                                            title="Reject question"
                                        >
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>

                                    {{-- View Details Button --}}
                                    <a 
                                        href="{{ route('moderator.questions.show', $question->id) }}" 
                                        class="btn btn-sm btn-info"
                                        title="View details"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Unassign Button --}}
                                    <form 
                                        action="{{ route('moderator.questions.unassign', $question->id) }}" 
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to unassign this question?');"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="btn btn-sm btn-outline-warning"
                                            title="Unassign question"
                                        >
                                            <i class="fas fa-unlink me-1"></i>Unassign
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-4">
        <a 
            href="{{ route('moderator.exams.index') }}" 
            class="btn btn-outline-secondary"
        >
            <i class="fas fa-arrow-left me-2"></i>Back to Exams
        </a>
    </div>
</div>
@endsection

@extends('layouts.student')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-3">{{ $exam->name }}</h1>
        <p>{{ $exam->description }}</p>

        <div class="mb-4">
            <strong>Duration:</strong> {{ $exam->duration }} minutes<br>

            @if($exam->type === 'scheduled')
                <strong>Application Period:</strong>
                @if ($exam->application_start && $exam->application_end)
                    {{ $exam->application_start->format('d M Y, h:i A') }} - {{ $exam->application_end->format('d M Y, h:i A') }}
                @else
                    <em>Application period not set</em>
                @endif
                <br>
            @endif

            <strong>Exam Start:</strong> 
            {{ $exam->exam_start ? $exam->exam_start->format('d M Y, h:i A') : 'Not specified' }}<br>

            <strong>Exam End:</strong> 
            {{ $exam->exam_end ? $exam->exam_end->format('d M Y, h:i A') : 'Not specified' }}
        </div>

        @auth
            @if(auth()->user()->hasRole('student'))
                @if($exam->canApply())
                    <form action="{{ route('student.exams.apply', $exam) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary">Apply Now</button>
                    </form>
                @elseif($exam->canJoinExam())
                    <a href="{{ route('student.exams.start', $exam) }}" class="btn btn-success mb-3">Enter Exam</a>
                @else
                    <p class="text-muted">Exam access closed</p>
                @endif
            @endif
        @endauth

        <a href="{{ route('student.exams.index') }}" class="btn btn-secondary">Back to Exams</a>
    </div>
@endsection

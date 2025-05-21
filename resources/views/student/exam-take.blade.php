@extends('layouts.student')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-3">Exam: {{ $exam->name }}</h1>
        <p>{{ $exam->description }}</p>

        <div class="mb-4">
            <strong>Duration:</strong> {{ $exam->duration }} minutes<br>

            @if($exam->type === 'scheduled')
                <strong>Scheduled Time:</strong><br>
                {{ $exam->exam_start ? $exam->exam_start->format('d M Y, h:i A') : 'Not specified' }} - 
                {{ $exam->exam_end ? $exam->exam_end->format('d M Y, h:i A') : 'Not specified' }}
            @endif
        </div>

        <form action="{{ route('student.exams.submit', $exam) }}" method="POST">
            @csrf

            {{-- Placeholder for Questions --}}
            <div class="alert alert-info">
                Questions will be displayed here.
            </div>

            {{-- You can loop through questions like this (if you pass them): --}}
            {{--
            @foreach($questions as $index => $question)
                <div class="mb-4">
                    <h5>Q{{ $index + 1 }}. {{ $question->text }}</h5>

                    @foreach($question->options as $option)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" id="q{{ $question->id }}_{{ $option->id }}">
                            <label class="form-check-label" for="q{{ $question->id }}_{{ $option->id }}">
                                {{ $option->text }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach
            --}}

            <button type="submit" class="btn btn-success">Submit Exam</button>
        </form>

        <a href="{{ route('student.exams.index') }}" class="btn btn-secondary mt-3">Back to Exams</a>
    </div>
@endsection

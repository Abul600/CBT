{{-- resources/views/student/exam-take.blade.php --}}

@extends('layouts.student') {{-- Adjust if your layout file is different --}}

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

            @foreach($exam->questions as $index => $question)
                <div class="mb-4">
                    <h5>Q{{ $index + 1 }}. {{ $question->question_text }}</h5>

                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="answers[{{ $question->id }}]" 
                            value="option_a" 
                            id="q{{ $question->id }}_option_a"
                        >
                        <label class="form-check-label" for="q{{ $question->id }}_option_a">
                            {{ $question->option_a }}
                        </label>
                    </div>

                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="answers[{{ $question->id }}]" 
                            value="option_b" 
                            id="q{{ $question->id }}_option_b"
                        >
                        <label class="form-check-label" for="q{{ $question->id }}_option_b">
                            {{ $question->option_b }}
                        </label>
                    </div>

                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="answers[{{ $question->id }}]" 
                            value="option_c" 
                            id="q{{ $question->id }}_option_c"
                        >
                        <label class="form-check-label" for="q{{ $question->id }}_option_c">
                            {{ $question->option_c }}
                        </label>
                    </div>

                    <div class="form-check">
                        <input 
                            class="form-check-input" 
                            type="radio" 
                            name="answers[{{ $question->id }}]" 
                            value="option_d" 
                            id="q{{ $question->id }}_option_d"
                        >
                        <label class="form-check-label" for="q{{ $question->id }}_option_d">
                            {{ $question->option_d }}
                        </label>
                    </div>
                </div>
                <hr>
            @endforeach

            <button type="submit" class="btn btn-success">Submit Exam</button>
        </form>

        <a href="{{ route('student.exams.index') }}" class="btn btn-secondary mt-3">Back to Exams</a>
    </div>
@endsection

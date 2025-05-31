@extends('layouts.student')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>{{ $exam->name }}</h4>
            <div id="progress" class="text-muted">
                Question <span id="current-q">1</span> of {{ $exam->questions->count() }}
            </div>
        </div>

        <form action="{{ route('student.exams.submit', $exam) }}" method="POST" id="examForm">
            @csrf
            <div class="card-body">
                @foreach($exam->questions as $index => $question)
                    <div class="question-container" data-question="{{ $index + 1 }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                        <div class="mb-4">
                            <h5>Q{{ $index + 1 }}. {{ $question->question_text }}</h5>
                        </div>

                        {{-- Descriptive Question --}}
                        @if($question->type === 'descriptive')
                            <textarea class="form-control" name="answers[{{ $question->id }}]" rows="5"
                                      placeholder="Type your answer here (700 characters max)"
                                      required>{{ old("answers.{$question->id}") }}</textarea>

                        {{-- Multiple Choice Question --}}
                        @else
                            @foreach(['a', 'b', 'c', 'd'] as $option)
                                @php $optionKey = "option_{$option}"; @endphp
                                @if(!empty($question->$optionKey))
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[{{ $question->id }}]" 
                                               value="{{ $option }}" 
                                               id="q{{ $question->id }}_{{ $option }}"
                                               {{ old("answers.{$question->id}") == $option ? 'checked' : '' }}
                                               required>
                                        <label class="form-check-label" for="q{{ $question->id }}_{{ $option }}">
                                            {{ $question->$optionKey }}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="card-footer d-flex justify-content-between">
                <button type="button" class="btn btn-prev me-3" id="prevBtn" disabled>Previous</button>
                <button type="button" class="btn btn-primary me-3" id="nextBtn">Next</button>
                <button type="submit" class="btn btn-submit" id="submitBtn" style="display: none;">Submit Exam</button>
            </div>
        </form>
    </div>
</div>

<style>
    .question-container { min-height: 300px; }
    .form-check-input { transform: scale(1.2); margin-right: 10px; }
    .form-check-label { font-size: 1rem; }
    #progress { font-size: 1rem; }

    /* Previous Button Style */
    .btn-prev {
        background: linear-gradient(45deg, #6a11cb, #2575fc);
        color: white;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        box-shadow: 0 0 8px #2575fc;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .btn-prev:disabled {
        background: #ccc;
        box-shadow: none;
        cursor: not-allowed;
        color: #666;
    }
    .btn-prev:not(:disabled):hover {
        background: linear-gradient(45deg, #2575fc, #6a11cb);
        box-shadow: 0 0 15px #6a11cb;
        transform: scale(1.05);
    }

    /* Submit Button Style */
    .btn-submit {
        background: linear-gradient(45deg, #00c853, #b2ff59);
        color: #1b5e20;
        padding: 0.5rem 1.5rem;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        box-shadow: 0 0 12px #00c853;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .btn-submit:hover {
        background: linear-gradient(45deg, #b2ff59, #00c853);
        box-shadow: 0 0 20px #b2ff59;
        transform: scale(1.1);
        color: #003300;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let currentQuestion = 1;
        const totalQuestions = {{ $exam->questions->count() }};

        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        const showQuestion = (num) => {
            document.querySelectorAll('.question-container').forEach((el) => {
                el.style.display = 'none';
            });
            document.querySelector(`[data-question="${num}"]`).style.display = 'block';
            document.getElementById('current-q').textContent = num;

            prevBtn.disabled = num === 1;
            nextBtn.style.display = num === totalQuestions ? 'none' : 'inline-block';
            submitBtn.style.display = num === totalQuestions ? 'inline-block' : 'none';
        };

        nextBtn.addEventListener('click', () => {
            if (currentQuestion < totalQuestions) {
                currentQuestion++;
                showQuestion(currentQuestion);
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentQuestion > 1) {
                currentQuestion--;
                showQuestion(currentQuestion);
            }
        });

        showQuestion(currentQuestion);
    });
</script>
@endsection

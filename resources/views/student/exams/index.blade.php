@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 mt-4">
    @if(isset($examToStart))
        {{-- ------------------- Exam Start View ------------------- --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>{{ $examToStart->name }}</h4>
                <div id="progress" class="text-muted">
                    Question <span id="current-q">1</span> of {{ $examToStart->questions->count() }}
                </div>
            </div>

            <form action="{{ route('student.exams.submit', $examToStart) }}" method="POST" id="examForm">
                @csrf
                <div class="card-body">
                    @foreach($examToStart->questions as $index => $question)
                        <div class="question-container" data-question="{{ $index + 1 }}" style="display: {{ $loop->first ? 'block' : 'none' }};">
                            <div class="mb-4">
                                <h5>Q{{ $index + 1 }}. {{ $question->question_text }}</h5>
                            </div>

                            @if($question->type === 'descriptive')
                                <textarea class="form-control" name="answers[{{ $question->id }}]" rows="5"
                                          placeholder="Type your answer here (700 characters max)"
                                          required>{{ old("answers.{$question->id}") }}</textarea>
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
                    <button type="button" class="btn btn-secondary" id="prevBtn" disabled>Previous</button>
                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">Submit Exam</button>
                </div>
            </form>
        </div>

        <a href="{{ route('student.exams.index') }}" class="btn btn-link mt-3">Back to Exams</a>

        <style>
            .question-container { min-height: 300px; }
            .form-check-input { transform: scale(1.2); margin-right: 10px; }
            .form-check-label { font-size: 1rem; }
            #progress { font-size: 1rem; }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                let currentQuestion = 1;
                const totalQuestions = {{ $examToStart->questions->count() }};

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
    @else
        {{-- ------------------- Exam List View ------------------- --}}
        <h2 class="text-2xl font-bold mb-6">Available Exams</h2>

        <form method="GET" action="{{ route('student.take.exam') }}" class="mb-6">
            <label for="district" class="block text-lg font-medium mb-2">Select District:</label>
            <select name="district" id="district" class="border border-gray-300 rounded p-2 w-full sm:w-1/3" onchange="this.form.submit()">
                <option value="">All Districts</option>
                @foreach($districts as $district)
                    <option value="{{ $district->id }}" {{ $selectedDistrict == $district->id ? 'selected' : '' }}>
                        {{ $district->name }}
                    </option>
                @endforeach
            </select>
        </form>

        @if($exams->isEmpty())
            <p class="text-gray-600">No exams available for the selected district.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($exams as $exam)
                    <div class="bg-white border border-gray-200 p-6 rounded-lg shadow">
                        <h3 class="text-xl font-semibold">{{ $exam->name }}</h3>

                        <div class="mt-2 text-sm text-gray-700">
                            <p><strong>District:</strong> {{ $exam->district->name ?? 'N/A' }}</p>
                            <p><strong>Duration:</strong> {{ $exam->duration }} minutes</p>

                            <p><strong>Type:</strong>
                                @if($exam->isConverted())
                                    <span class="inline-block bg-gray-200 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full">
                                        Converted Mock Exam
                                    </span>
                                @elseif($exam->type === 'mock')
                                    <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">
                                        Mock Test
                                    </span>
                                @else
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">
                                        Scheduled Exam
                                    </span>
                                @endif
                            </p>

                            @if($exam->type === 'scheduled' && !$exam->isConverted())
                                <p class="mt-2"><strong>Schedule:</strong>
                                    {{ $exam->exam_start->format('d M Y, h:i A') }} - {{ $exam->exam_end->format('d M Y, h:i A') }}
                                </p>
                            @elseif($exam->isConverted())
                                <p class="text-sm text-gray-600 mt-2">
                                    Originally scheduled: {{ $exam->exam_start->format('d M Y, h:i A') }}
                                </p>
                            @endif
                        </div>

                        {{-- Modified Action Buttons --}}
                        <div class="mt-4">
                            @if($exam->isConverted() || $exam->type === 'mock' || $exam->isCurrentlyRunning())
                                <a href="{{ route('student.exams.start', $exam) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                    {{ $exam->isConverted() ? 'Start Now' : 'Start Exam' }}
                                </a>
                            @elseif($exam->hasApplied(auth()->user()))
                                <span class="bg-blue-500 text-white px-4 py-2 rounded">
                                    Applied ✓
                                </span>
                            @elseif($exam->canApply())
                                <form method="POST" action="{{ route('student.exam.apply', $exam) }}">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                        Apply Now
                                    </button>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Closes {{ $exam->application_end->setTimezone(auth()->user()->timezone)->format('d M Y, h:i A') }}
                                    </p>
                                </form>
                            @else
                                <p class="text-sm text-red-600 font-medium">
                                    Applications closed
                                    @if($exam->application_end)
                                        ({{ $exam->application_end->setTimezone(auth()->user()->timezone)->format('d M Y, h:i A') }})
                                    @endif
                                </p>
                            @endif

                            <a href="{{ route('student.exams.view', $exam) }}" 
                               class="inline-block mt-4 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                View Details
                            </a>
                        </div>

                        @if($exam->converted_at)
                            <p class="text-sm text-gray-500 mt-2">
                                Converted: {{ $exam->converted_at->format('d M Y, h:i A') }}
                            </p>
                        @endif

                        <div class="debug-info text-gray-500 text-sm mt-4 border-t pt-2">
                            <div>Exam ID: {{ $exam->id }}</div>
                            <div>App Start: {{ $exam->application_start?->format('Y-m-d H:i:s T') ?? 'N/A' }}</div>
                            <div>App End: {{ $exam->application_end?->format('Y-m-d H:i:s T') ?? 'N/A' }}</div>
                            <div>Now: {{ now()->format('Y-m-d H:i:s T') }}</div>
                            <div>Can Apply: {{ $exam->canApply() ? 'YES' : 'NO' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
@endsection

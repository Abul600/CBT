@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">Available Exams</h2>

    <!-- District Selector -->
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

    <!-- Exams List -->
    @if($exams->isEmpty())
        <p class="text-gray-600">No exams available for the selected district.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($exams as $exam)
                <div class="bg-white border border-gray-200 p-6 rounded-lg shadow">
                    <h3 class="text-xl font-semibold">{{ $exam->name }}</h3>
                    <p class="mt-2"><strong>District:</strong> {{ $exam->district->name ?? 'N/A' }}</p>
                    <p class="mt-2"><strong>Type:</strong>
                        @if($exam->type === 'mock')
                            <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold px-3 py-1 rounded-full">Mock Test</span>
                        @else
                            <span class="inline-block bg-yellow-100 text-yellow-800 text-sm font-semibold px-3 py-1 rounded-full">Scheduled Exam</span>
                        @endif
                    </p>
                    <p class="mt-1"><strong>Duration:</strong> {{ $exam->duration }} minutes</p>
                    @if($exam->type === 'scheduled')
                        <p class="mt-2"><strong>Start Time:</strong>
                            {{ $exam->exam_start ? $exam->exam_start->format('d M Y, h:i A') : 'Not scheduled' }}
                        </p>
                    @endif

                    <!-- Status & Application Section -->
                    <div class="mt-4">
                        @if($exam->type === 'mock')
                            <!-- Mock Exam - Can start anytime -->
                            <a href="{{ route('exams.mock.start', $exam) }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Start Now
                            </a>
                        @else
                            <!-- Scheduled Exam -->
                            @if($exam->canApply())
                                <form method="POST" action="{{ route('student.exam.apply', $exam) }}">
                                    @csrf
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                        Apply Now
                                    </button>
                                </form>
                                <p class="text-sm text-gray-600 mt-1">
                                    Apply before {{ $exam->application_end->format('d M Y, h:i A') }}
                                </p>
                            @elseif(auth()->user()->appliedExams->contains($exam))
                                <button class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed" disabled>
                                    Applied ✓
                                </button>
                            @else
                                <p class="text-sm text-red-600 font-medium">Applications closed ({{ $exam->application_end->format('d M Y, h:i A') }})</p>
                            @endif

                            <!-- View/Start Button -->
                            <a href="{{ route('student.exams.view', $exam) }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                View Exam
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

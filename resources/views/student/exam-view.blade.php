@extends('layouts.student')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold mb-4 text-black">{{ $exam->name }}</h1>

        <p class="mb-4 text-black">{{ $exam->description }}</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 text-black">
            @if ($exam->district)
                <div>
                    <p class="font-semibold">District:</p>
                    <p>{{ $exam->district->name }}</p>
                </div>
            @endif

            <div>
                <p class="font-semibold">Duration:</p>
                <p>{{ $exam->duration }} minutes</p>
            </div>

            <div>
                <p class="font-semibold">Total Questions:</p>
                <p>{{ $exam->questions_count ?? $exam->questions->count() }}</p>
            </div>

            <div>
                <p class="font-semibold">Exam Type:</p>
                <p class="capitalize">{{ $exam->type }}</p>
            </div>

            @if ($exam->type === 'scheduled')
                <div class="col-span-2">
                    <p class="font-semibold">Application Period:</p>
                    @if ($exam->application_start && $exam->application_end)
                        <p>
                            {{ $exam->application_start->format('d M Y, h:i A') }} - 
                            {{ $exam->application_end->format('d M Y, h:i A') }}
                        </p>
                    @else
                        <p class="italic">Application period not set</p>
                    @endif
                </div>
            @endif

            <div>
                <p class="font-semibold">Exam Start:</p>
                <p>
                    {{ $exam->exam_start ? $exam->exam_start->format('d M Y, h:i A') : 'Not specified' }}
                </p>
            </div>

            <div>
                <p class="font-semibold">Exam End:</p>
                <p>
                    {{ $exam->exam_end ? $exam->exam_end->format('d M Y, h:i A') : 'Not specified' }}
                </p>
            </div>
        </div>

        @auth
            @if(auth()->user()->hasRole('student'))
                <div class="mt-6">
                    @if ($exam->type === 'mock')
                        <a href="{{ route('student.exams.start', $exam) }}"
                           class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                            Start Mock Test
                        </a>
                    @elseif($exam->canApply())
                        <form action="{{ route('student.exams.apply', $exam) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                                Apply Now
                            </button>
                        </form>
                    @elseif($exam->isCurrentlyRunning())
                        <a href="{{ route('student.exams.start', $exam) }}"
                           class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                            Enter Exam
                        </a>
                    @else
                        <p class="italic text-black">Exam access closed</p>
                    @endif
                </div>
            @endif
        @endauth

        <div class="mt-4">
            <a href="{{ route('student.exams.index') }}"
               class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700">
                Back to Exams
            </a>
        </div>
    </div>
</div>
@endsection

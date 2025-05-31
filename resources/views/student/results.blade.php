@extends('layouts.student')

@section('content')
<h1 class="text-4xl font-extrabold mb-8 text-white drop-shadow-md tracking-wide">
    Your Exam Results
</h1>

@if($results->isNotEmpty())
    <ul class="space-y-6">
        @foreach ($results as $result)
            <li class="bg-white border border-gray-200 rounded-xl p-6 shadow hover:shadow-lg transition-shadow duration-300">
                <p class="text-lg font-semibold text-gray-800 mb-2">
                    Exam: <span class="font-normal">{{ $result->exam->title ?? 'Unknown' }}</span>
                </p>
                <p class="text-lg font-semibold text-green-700 mb-4">
                    Score: <span class="font-normal">{{ $result->score ?? 'N/A' }}</span>
                </p>
                <a href="{{ route('student.results.show', $result->id) }}"
                   class="inline-block text-white bg-blue-600 hover:bg-blue-700 rounded-full px-5 py-2 font-semibold shadow-md hover:shadow-lg transition-all duration-300">
                    View Details
                </a>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-gray-300 italic text-lg mt-6">You have not completed any exams yet.</p>
@endif
@endsection

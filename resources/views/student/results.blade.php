@extends('layouts.student')

@section('content')
<h1 class="text-2xl font-bold mb-6">Your Exam Results</h1>

@if($results->isNotEmpty())
    <ul class="space-y-4">
        @foreach ($results as $result)
            <li class="bg-white border rounded p-4 shadow">
                <p><strong>Exam:</strong> {{ $result->exam->title ?? 'Unknown' }}</p>
                <p><strong>Score:</strong> {{ $result->score ?? 'N/A' }}</p>
                <a href="{{ route('student.results.view', $result->exam_id) }}" class="text-blue-600 hover:underline">
                    View Details
                </a>
            </li>
        @endforeach
    </ul>
@else
    <p class="text-gray-600">You have not completed any exams yet.</p>
@endif
@endsection

@extends('layouts.student')

@section('content')
    <h1>Result Details</h1>

    @if($result)
        <p>Exam: {{ $result->exam->title ?? 'Unknown' }}</p>
        <p>Score: {{ $result->score ?? 'N/A' }}</p>
        <p>Completed on: {{ $result->created_at->format('d M Y') }}</p>
    @else
        <p>Result not found.</p>
    @endif

    {{-- Fixed link: --}}
    <a href="{{ url('/student/view-results') }}">Back to Results</a>
@endsection

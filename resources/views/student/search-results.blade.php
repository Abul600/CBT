@extends('layouts.student')

@section('content')
<h1>Search Results</h1>

@if($results->count())
    <ul>
        @foreach ($results as $question)
            <li>{{ $question->content }}</li>
        @endforeach
    </ul>
@else
    <p>No questions found matching your search.</p>
@endif
@endsection

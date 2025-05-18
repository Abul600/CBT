@extends('layouts.student')

@section('content')
<h1>{{ $exam->title }}</h1>
<p>{{ $exam->description }}</p>

<a href="{{ route('student.exams.index') }}">Back to Exams</a>

<!--
You can add exam questions here for the student to take if needed,
or a button to start the exam.
-->
@endsection

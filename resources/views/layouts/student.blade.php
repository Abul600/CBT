@extends('layouts.base')

@section('content')
    <div class="container">
        <h1>Student Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}! Access your exams and results here.</p>
        <!-- Student-specific content -->
    </div>
@endsection

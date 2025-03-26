@extends('layouts.base')

@section('content')
    <div class="container">
        <h1>Moderator Dashboard</h1>
        <p>Hello, {{ auth()->user()->name }}! Manage exams and paper setters.</p>
        <!-- Moderator-specific content -->
    </div>
@endsection

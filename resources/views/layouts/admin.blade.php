@extends('layouts.base')

@section('content')
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, {{ auth()->user()->name }}! You have full control.</p>
        <!-- Admin-specific content -->
    </div>
@endsection

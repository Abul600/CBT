@extends('layouts.paper_setter')

@section('content')
    <h2>Create Exam</h2>
    <p>Fill in the details below to create a new exam.</p>

    <form action="{{ route('paper_setter.exams.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Exam Name:</label>
            <input type="text" name="name" id="name" required>
        </div>

        <button type="submit">Create Exam</button>
    </form>
@endsection
 

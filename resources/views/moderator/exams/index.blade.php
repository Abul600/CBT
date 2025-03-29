@extends('layouts.paper_setter')

@section('content')
    <h2>Manage Exams</h2>
    <p>Here you can manage your exams.</p>

    <a href="{{ route('paper_setter.exams.create') }}" class="btn btn-primary">Add Exam</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Exam Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop through exams --}}
            @foreach($exams ?? [] as $exam)
                <tr>
                    <td>{{ $exam->id }}</td>
                    <td>{{ $exam->name }}</td>
                    <td>
                        <form action="{{ route('paper_setter.exams.destroy', $exam->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection


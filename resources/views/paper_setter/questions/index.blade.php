@extends('layouts.paper_setter')

@section('content')
    <h2>Manage Questions</h2>
    <p>Here you can manage your exam questions.</p>

    <a href="{{ route('paper_setter.questions.create') }}" class="btn btn-primary">Add Question</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Question</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop through questions --}}
            @foreach($questions ?? [] as $question)
                <tr>
                    <td>{{ $question->id }}</td>
                    <td>{{ $question->text }}</td>
                    <td>
                        <form action="{{ route('paper_setter.questions.destroy', $question->id) }}" method="POST">
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

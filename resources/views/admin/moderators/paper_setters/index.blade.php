@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Paper Seaters</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('moderator.paper_seaters.create') }}" class="btn btn-primary mb-3">Assign New Paper Seater</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Moderator</th>
                <th>Paper Seater</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paperSeaters as $paperSeater)
            <tr>
                <td>{{ $paperSeater->id }}</td>
                <td>{{ $paperSeater->moderator->name }}</td>
                <td>{{ $paperSeater->user->name }}</td>
                <td>
                    <form action="{{ route('moderator.paper_seaters.destroy', $paperSeater->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                            Remove
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No paper seaters assigned yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

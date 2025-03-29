@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Paper Setters</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('moderator.paper_setters.create') }}" class="btn btn-primary mb-3">Assign New Paper Setter</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Moderator</th>
                <th>Paper Setter</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paperSetters as $paperSetter)
            <tr>
                <td>{{ $paperSetter->id }}</td>
                <td>{{ $paperSetter->moderator->name }}</td>
                <td>{{ $paperSetter->user->name }}</td>
                <td>
                    <form action="{{ route('moderator.paper_setters.destroy', $paperSetter->id) }}" method="POST">
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
                <td colspan="4" class="text-center">No paper setters assigned yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@extends('layouts.moderator')

@section('content')
<div class="container">
    <h2>Edit Paper Setter</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('moderator.paper_setters.update', $paperSetter->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- Required for update requests -->

        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $paperSetter->name) }}" required class="form-control">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email', $paperSetter->email) }}" required class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('moderator.paper_setters.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

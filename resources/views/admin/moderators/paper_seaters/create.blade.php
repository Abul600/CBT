@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Assign Paper Seater</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('moderator.paper_seaters.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="moderator_id" class="form-label">Select Moderator</label>
            <select name="moderator_id" id="moderator_id" class="form-control" required>
                <option value="">-- Select Moderator --</option>
                @foreach($moderators as $moderator)
                    <option value="{{ $moderator->id }}">{{ $moderator->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="user_id" class="form-label">Select Paper Seater</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">-- Select Paper Seater --</option>
                @foreach($paperSetters as $paperSetter)
                    <option value="{{ $paperSetter->id }}">{{ $paperSetter->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Assign Paper Seater</button>
        <a href="{{ route('moderator.paper_seaters.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection

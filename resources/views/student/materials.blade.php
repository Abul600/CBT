@extends('layouts.student')

@section('content')
<h1>Study Materials</h1>

@if($materials->count())
    <ul>
        @foreach ($materials as $material)
            <li>
                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank">{{ $material->title }}</a>
            </li>
        @endforeach
    </ul>
@else
    <p>No study materials available right now.</p>
@endif
@endsection

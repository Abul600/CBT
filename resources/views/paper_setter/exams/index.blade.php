@extends('layouts.paper-setter')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Exams Needing Grading</h1>
    
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Exam Name</th>
                    <th class="px-6 py-3 text-left">Ungraded Answers</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $exam->name }}</td>
                    <td class="px-6 py-4">{{ $exam->ungraded_count }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('paper-setter.exams.answers', $exam) }}" 
                           class="text-blue-600 hover:text-blue-900">
                            Grade Answers
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center">No exams need grading</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $exams->links() }}
    </div>
</div>
@endsection
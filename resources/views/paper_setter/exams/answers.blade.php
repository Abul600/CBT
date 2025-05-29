@extends('layouts.paper-setter')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Grade Answers for {{ $exam->name }}</h1>
    
    <form method="POST" action="{{ route('paper-setter.bulk-grade', $exam) }}">
        @csrf
        
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Student</th>
                        <th class="px-6 py-3 text-left">Question</th>
                        <th class="px-6 py-3 text-left">Answer</th>
                        <th class="px-6 py-3 text-left">Max Marks</th>
                        <th class="px-6 py-3 text-left">Marks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($answers as $answer)
                    <tr class="border-b">
                        <td class="px-6 py-4">{{ $answer->user->name }}</td>
                        <td class="px-6 py-4">{{ Str::limit($answer->question->question_text, 50) }}</td>
                        <td class="px-6 py-4">{{ Str::limit($answer->answer, 100) }}</td>
                        <td class="px-6 py-4">{{ $answer->question->marks }}</td>
                        <td class="px-6 py-4">
                            <input type="number" 
                                   name="marks[{{ $answer->id }}]" 
                                   class="w-20 border rounded p-2"
                                   min="0" 
                                   max="{{ $answer->question->marks }}"
                                   required>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button type="submit" 
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Submit All Marks
            </button>
        </div>
    </form>

    <div class="mt-4">
        {{ $answers->links() }}
    </div>
</div>
@endsection
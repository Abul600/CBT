@extends('layouts.paper_setter')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Grade Submissions: {{ $exam->name }}</h1>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            @if($submissions->isEmpty())
                <p class="text-gray-600">No submissions require grading.</p>
            @else
                <div class="space-y-6">
                    @foreach($submissions as $submission)
                    <div class="border rounded-lg p-4">
                        <h3 class="text-lg font-medium mb-2">
                            Student: {{ $submission->student->name }}
                        </h3>
                        
                        <form action="{{ route('paper_setter.answers.update', $answer) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-4">
                                @foreach($submission->answers as $answer)
                                <div class="border-t pt-4">
                                    <h4 class="font-medium mb-2">Question: {{ $answer->question->text }}</h4>
                                    <p class="mb-2"><strong>Answer:</strong> {{ $answer->answer_text }}</p>
                                    
                                    <div class="flex items-center">
                                        <label class="block mr-4">
                                            <span class="text-gray-700">Marks:</span>
                                            <input type="number" name="marks" 
                                                   min="0" max="{{ $answer->question->marks }}"
                                                   value="{{ $answer->marks ?? 0 }}"
                                                   class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm">
                                        </label>
                                        <span class="text-gray-500">
                                            / {{ $answer->question->marks }} marks
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                                    Save Marks
                                </button>
                            </div>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
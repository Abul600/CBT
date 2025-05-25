@extends('layouts.moderator')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-4">Question Details</h1>
        
        <div class="space-y-4">
            <!-- Question Type Badge -->
            <div class="mb-4">
                <span class="px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-sm">
                    {{ strtoupper($question->type) }}
                </span>
            </div>

            <!-- Question Text -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Question:</label>
                <p class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                    {{ $question->question_text }}
                </p>
            </div>

            <!-- MCQ Options (Only for MCQ types) -->
            @if(in_array(strtolower($question->type), ['mcq1', 'mcq2']))
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-3">Options:</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach(['a', 'b', 'c', 'd'] as $option)
                        @if(!empty($question->{"option_$option"}))
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="font-medium">Option {{ strtoupper($option) }}:</span>
                            {{ $question->{"option_$option"} }}
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Back Button -->
            <div class="mt-6">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Questions
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
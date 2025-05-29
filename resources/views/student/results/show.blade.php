@extends('layouts.student')

@section('content')
<div class="container py-8">
    <div class="card shadow-lg">
        <div class="card-header bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-6">
            <h1 class="text-2xl font-bold text-center">Exam Result</h1>
            <h2 class="text-xl text-center mt-2">{{ $examName }}</h2>
        </div>
        
        <div class="card-body p-6">
            <!-- Score Summary -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-blue-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Score Card -->
                    <div class="bg-blue-50 p-5 rounded-lg border border-blue-200">
                        <h3 class="text-lg font-semibold text-blue-800 mb-2">Your Score</h3>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ $result->score }} <span class="text-gray-500 text-xl">/ {{ $totalMarks }}</span>
                        </p>
                    </div>
                    
                    <!-- Percentage Card -->
                    <div class="bg-purple-50 p-5 rounded-lg border border-purple-200">
                        <h3 class="text-lg font-semibold text-purple-800 mb-2">Percentage</h3>
                        <p class="text-3xl font-bold text-purple-600">
                            @if($totalMarks > 0)
                                {{ number_format(($result->score / $totalMarks) * 100, 1) }}%
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    
                    <!-- Status Card -->
                    <div class="bg-green-50 p-5 rounded-lg border border-green-200">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Status</h3>
                        <p class="text-2xl font-bold {{ $result->passed ? 'text-green-600' : 'text-red-600' }}">
                            {{ $result->passed ? 'Passed' : 'Not Passed' }}
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Detailed Results -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6 border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Detailed Results</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- MCQ Section -->
                    <div class="bg-yellow-50 p-5 rounded-lg border border-yellow-200">
                        <h4 class="text-lg font-medium text-yellow-800 mb-2">MCQ Section</h4>
                        <p class="text-xl">
                            <span class="font-bold">{{ $result->mcq_score ?? 0 }}</span> 
                            out of <span class="font-bold">{{ $mcqTotal }}</span>
                        </p>
                    </div>
                    
                    <!-- Descriptive Section -->
                    <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-200">
                        <h4 class="text-lg font-medium text-indigo-800 mb-2">Descriptive Section</h4>
                        <p class="text-xl">
                            <span class="font-bold">{{ $result->descriptive_score ?? 0 }}</span> 
                            out of <span class="font-bold">{{ $descriptiveTotal }}</span>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Release Information -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Result Status</h3>
                
                @if($result->exam && $result->exam->released_at)
                    <p class="text-lg text-gray-800">
                        <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                        Results released on: {{ $result->exam->released_at->format('M d, Y h:i A') }}
                    </p>
                @else
                    <div class="flex items-center">
                        <i class="fas fa-clock text-yellow-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-lg font-medium text-yellow-700">Results not yet released</p>
                            <p class="text-gray-600 mt-1">
                                Your results are being processed and will be available soon.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="card-footer bg-gray-100 px-6 py-4 flex justify-center">
            <a href="{{ route('student.dashboard') }}" 
               class="btn btn-primary px-8 py-3 text-lg font-medium">
                <i class="fas fa-home mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

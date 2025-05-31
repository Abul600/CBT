@extends('layouts.student')

@section('content')
<div class="container py-10">
    <div class="rounded-2xl shadow-2xl overflow-hidden bg-gradient-to-br from-indigo-100 via-purple-50 to-white border border-indigo-200">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-700 to-indigo-900 text-white py-8 px-6 text-center shadow-md">
            <h1 class="text-4xl font-extrabold tracking-wide drop-shadow-lg">🎓 Exam Result</h1>
            <h2 class="text-2xl font-semibold mt-2 drop-shadow-sm">{{ $examName }}</h2>
        </div>

        <!-- Body -->
        <div class="p-8 space-y-10 bg-white bg-opacity-90">

            <!-- Score Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
                <!-- Score -->
                <div class="bg-blue-100 border-l-8 border-blue-500 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                    <h3 class="text-xl font-bold text-blue-800 mb-2">Your Score</h3>
                    <p class="text-4xl font-extrabold text-blue-600">
                        {{ $result->score }} <span class="text-gray-500 text-2xl">/ {{ $totalMarks }}</span>
                    </p>
                </div>

                <!-- Percentage -->
                <div class="bg-purple-100 border-l-8 border-purple-500 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                    <h3 class="text-xl font-bold text-purple-800 mb-2">Percentage</h3>
                    <p class="text-4xl font-extrabold text-purple-600">
                        @if($totalMarks > 0)
                            {{ number_format(($result->score / $totalMarks) * 100, 1) }}%
                        @else
                            N/A
                        @endif
                    </p>
                </div>

                <!-- Status -->
                <div class="bg-green-100 border-l-8 border-green-500 p-6 rounded-xl shadow-lg hover:shadow-2xl transition">
                    <h3 class="text-xl font-bold text-green-800 mb-2">Status</h3>
                    <p class="text-3xl font-bold {{ $result->passed ? 'text-green-600' : 'text-red-600' }}">
                        {{ $result->passed ? 'Passed ✅' : 'Not Passed ❌' }}
                    </p>
                </div>
            </div>

            <!-- Detailed Section Scores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- MCQ -->
                <div class="bg-yellow-100 p-6 rounded-xl border-l-8 border-yellow-400 shadow-md hover:shadow-xl transition">
                    <h4 class="text-lg font-semibold text-yellow-800 mb-2">📝 MCQ Score</h4>
                    <p class="text-2xl text-yellow-700 font-bold">
                        {{ $result->mcq_score ?? 0 }} <span class="text-lg font-medium text-gray-600">/ {{ $mcqTotal }}</span>
                    </p>
                </div>

                <!-- Descriptive -->
                <div class="bg-indigo-100 p-6 rounded-xl border-l-8 border-indigo-400 shadow-md hover:shadow-xl transition">
                    <h4 class="text-lg font-semibold text-indigo-800 mb-2">📄 Descriptive Score</h4>
                    <p class="text-2xl text-indigo-700 font-bold">
                        {{ $result->descriptive_score ?? 0 }} <span class="text-lg font-medium text-gray-600">/ {{ $descriptiveTotal }}</span>
                    </p>
                </div>
            </div>

            <!-- Release Info -->
            <div class="bg-pink-50 p-6 rounded-xl border border-pink-200 shadow">
                <h3 class="text-lg font-semibold text-pink-800 mb-3">📅 Result Status</h3>
                @if($result->exam && $result->exam->released_at)
                    <p class="text-lg text-pink-700">
                        <i class="fas fa-calendar-check text-green-600 mr-2"></i>
                        Results released on: <strong>{{ $result->exam->released_at->format('M d, Y h:i A') }}</strong>
                    </p>
                @else
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                        <div>
                            <p class="text-lg font-semibold text-yellow-700">Results not yet released</p>
                            <p class="text-sm text-gray-600">Your results are being processed and will be available soon.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gradient-to-r from-gray-200 to-gray-300 px-6 py-5 flex justify-center">
            <a href="{{ route('student.dashboard') }}"
               class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-purple-700 hover:to-pink-600 text-white px-8 py-3 rounded-full text-lg font-semibold shadow-md hover:shadow-xl transition duration-300">
                <i class="fas fa-home mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

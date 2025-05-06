@extends('layouts.student')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-turquoise-500 via-teal-600 to-gold-500 flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto p-12 rounded-3xl shadow-2xl bg-gradient-to-br from-turquoise-500 via-teal-600 to-gold-500 animate-gradient-background border-8 border-solid border-teal-400">
        <!-- Header -->
        <div class="text-center mb-16">
        <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-500 to-orange-400 animate-pulse drop-shadow-md">

                🎓 Student Dashboard
            </h1>
            <p class="mt-4 text-xl text-gray-300">
                Dive into your learning experience with style.
            </p>
        </div>

        <!-- Card Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            <!-- Take Exam -->
            <a href="#"
               class="relative bg-gradient-to-r from-aqua-400 to-teal-500 rounded-3xl p-8 shadow-lg transform hover:scale-105 transition duration-300 border-4 border-transparent hover:border-aqua-400">
                <div class="absolute top-0 right-0 mt-4 mr-4 text-xs bg-aqua-100 text-aqua-800 dark:bg-aqua-900 dark:text-white px-3 py-1 rounded-full animate-bounce">
                    New!
                </div>
                <div class="flex items-center justify-center w-16 h-16 bg-white text-teal-700 rounded-full text-3xl mx-auto mb-6 shadow-inner">
                    📝
                </div>
                <h3 class="text-2xl font-bold text-center text-white">Take Exam</h3>
                <p class="text-center text-teal-100 mt-2">
                    Access and continue your assessments.
                </p>
            </a>

            <!-- View Results -->
            <a href="#"
               class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-3xl p-8 shadow-lg transform hover:scale-105 transition duration-300 border-4 border-transparent hover:border-green-400">
                <div class="flex items-center justify-center w-16 h-16 bg-white text-green-700 rounded-full text-3xl mx-auto mb-6 shadow-inner">
                    📊
                </div>
                <h3 class="text-2xl font-bold text-center text-white">View Results</h3>
                <p class="text-center text-green-100 mt-2">
                    Review your progress and scores.
                </p>
            </a>

            <!-- Study Materials -->
            <a href="#"
               class="bg-gradient-to-r from-aqua-500 to-teal-500 rounded-3xl p-8 shadow-lg transform hover:scale-105 transition duration-300 border-4 border-transparent hover:border-teal-400">
                <div class="flex items-center justify-center w-16 h-16 bg-white text-teal-700 rounded-full text-3xl mx-auto mb-6 shadow-inner">
                    📚
                </div>
                <h3 class="text-2xl font-bold text-center text-white">Study Materials</h3>
                <p class="text-center text-teal-100 mt-2">
                    Access notes, guides, and books.
                </p>
            </a>
        </div>
    </div>
</div>
@endsection

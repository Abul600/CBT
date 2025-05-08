@extends('layouts.student')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-slate-200 dark:from-slate-800 dark:via-slate-900 dark:to-black flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto p-12 rounded-3xl shadow-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-5xl font-extrabold text-slate-900 dark:text-white">
                🎓 Student Dashboard
            </h1>
            <p class="mt-4 text-xl text-slate-600 dark:text-slate-300">
                Dive into your learning experience with a focused workspace.
            </p>
        </div>

        <!-- Card Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
            <!-- Take Exam -->
            <a href="#"
               class="relative bg-gradient-to-r from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-700 rounded-3xl p-8 shadow-md transform hover:scale-105 transition duration-300 border border-transparent hover:border-indigo-300">
                <div class="absolute top-0 right-0 mt-4 mr-4 text-xs bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full animate-pulse">
                    New
                </div>
                <div class="flex items-center justify-center w-16 h-16 bg-white text-indigo-700 rounded-full text-3xl mx-auto mb-6 shadow-inner">
                    📝
                </div>
                <h3 class="text-2xl font-bold text-center text-white">Take Exam</h3>
                <p class="text-center text-indigo-100 mt-2">
                    Access and continue your assessments.
                </p>
            </a>

            <!-- View Results -->
            <a href="#"
               class="bg-gradient-to-r from-teal-500 to-teal-600 dark:from-teal-600 dark:to-teal-700 rounded-3xl p-8 shadow-md transform hover:scale-105 transition duration-300 border border-transparent hover:border-teal-300">
                <div class="flex items-center justify-center w-16 h-16 bg-white text-teal-700 rounded-full text-3xl mx-auto mb-6 shadow-inner">
                    📊
                </div>
                <h3 class="text-2xl font-bold text-center text-white">View Results</h3>
                <p class="text-center text-teal-100 mt-2">
                    Review your progress and scores.
                </p>
            </a>

            <!-- Study Materials -->
            <a href="#"
               class="bg-gradient-to-r from-sky-500 to-sky-600 dark:from-sky-600 dark:to-sky-700 rounded-3xl p-8 shadow-md transform hover:scale-105 transition duration-300 border border-transparent hover:border-sky-300">
                <div class="flex items-center justify-center w-16 h-16 bg-white text-sky-700 rounded-full text-3xl mx-auto mb-6 shadow-inner">
                    📚
                </div>
                <h3 class="text-2xl font-bold text-center text-white">Study Materials</h3>
                <p class="text-center text-sky-100 mt-2">
                    Access notes, guides, and books.
                </p>
            </a>
        </div>
    </div>
</div>
@endsection

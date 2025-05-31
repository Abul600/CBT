@extends('layouts.student')

@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-blue-50 py-10 min-h-screen">
    <div class="max-w-4xl mx-auto px-6">
        <h1 class="text-4xl font-extrabold mb-10 text-center text-indigo-800 drop-shadow-sm">
            📘 Study Materials
        </h1>

        @if($materials->count())
            <ul class="space-y-5 text-lg text-gray-800 bg-white p-6 rounded-xl shadow-md border border-indigo-100">
                @foreach ($materials as $material)
                    <li class="hover:bg-indigo-50 p-3 rounded transition duration-300 ease-in-out">
                        <a href="{{ asset('storage/' . $material->file_path) }}" 
                           target="_blank" 
                           class="text-indigo-700 font-semibold hover:text-indigo-900 hover:underline">
                            📄 {{ $material->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-lg text-gray-800 bg-white p-6 rounded-xl shadow-md border border-gray-200">
                <p class="mb-4 font-medium">Check out these helpful resources:</p>

                <!-- Online Learning Links -->
                <ul class="space-y-4 list-disc list-inside text-blue-800">
                    <li>
                        🌐 <a href="https://www.geeksforgeeks.org/computer-network-tutorials/" target="_blank" class="hover:underline font-semibold">GeeksforGeeks – Computer Networks Tutorials</a>
                    </li>
                    <li>
                        🎓 <a href="https://nptel.ac.in/courses/106105081" target="_blank" class="hover:underline font-semibold">NPTEL – Computer Networks (IIT Kharagpur)</a>
                    </li>
                </ul>

                <!-- AI, DSA, PCM Resources -->
                <h2 class="mt-10 text-xl font-bold text-indigo-700">🧠 Learning Resources for AI, DSA, and PCM</h2>
                <div class="space-y-6 mt-4">
                    <!-- AI -->
                    <div class="p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                        <h3 class="text-lg font-semibold">🤖 Artificial Intelligence</h3>
                        <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                            <li><a href="https://www.coursera.org/" target="_blank" class="text-blue-600 hover:underline">Coursera</a></li>
                            <li><a href="https://www.edx.org/" target="_blank" class="text-blue-600 hover:underline">edX</a></li>
                            <li><a href="https://www.fast.ai/" target="_blank" class="text-blue-600 hover:underline">Fast.ai</a></li>
                            <li><a href="https://ai.google/education/" target="_blank" class="text-blue-600 hover:underline">Google AI Education</a></li>
                            <li><a href="https://towardsdatascience.com/" target="_blank" class="text-blue-600 hover:underline">Towards Data Science</a></li>
                        </ul>
                    </div>

                    <!-- DSA -->
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h3 class="text-lg font-semibold">💻 Data Structures & Algorithms</h3>
                        <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                            <li><a href="https://www.geeksforgeeks.org/data-structures/" target="_blank" class="text-green-600 hover:underline">GeeksforGeeks</a></li>
                            <li><a href="https://leetcode.com/" target="_blank" class="text-green-600 hover:underline">LeetCode</a></li>
                            <li><a href="https://www.hackerrank.com/domains/tutorials/10-days-of-data-structures" target="_blank" class="text-green-600 hover:underline">HackerRank</a></li>
                            <li><a href="https://codeforces.com/" target="_blank" class="text-green-600 hover:underline">Codeforces</a></li>
                            <li><a href="https://ocw.mit.edu/courses/6-006-introduction-to-algorithms-spring-2020/" target="_blank" class="text-green-600 hover:underline">MIT OCW – Algorithms</a></li>
                        </ul>
                    </div>

                    <!-- PCM -->
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h3 class="text-lg font-semibold">🔬 Physics, Chemistry & Math</h3>
                        <ul class="list-disc list-inside text-gray-700 mt-2 space-y-1">
                            <li><a href="https://www.khanacademy.org/" target="_blank" class="text-yellow-600 hover:underline">Khan Academy</a></li>
                            <li><a href="https://nptel.ac.in/" target="_blank" class="text-yellow-600 hover:underline">NPTEL</a></li>
                            <li><a href="https://www.toppr.com/" target="_blank" class="text-yellow-600 hover:underline">Toppr</a></li>
                            <li><a href="https://www.byjus.com/" target="_blank" class="text-yellow-600 hover:underline">BYJU'S</a></li>
                            <li><a href="https://www.physicsgalaxy.com/" target="_blank" class="text-yellow-600 hover:underline">Physics Galaxy</a></li>
                            <li><a href="https://www.vedantu.com/" target="_blank" class="text-yellow-600 hover:underline">Vedantu</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Textbooks -->
                <h2 class="mt-10 text-xl font-bold text-indigo-700">📚 Recommended Textbooks</h2>
                <div class="space-y-6 mt-4">
                    <!-- Technical Books -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h3 class="text-lg font-semibold">📗 Data and Computer Communications – William Stallings</h3>
                        <p class="text-sm text-gray-700">Comprehensive guide on computer networks.</p>
                    </div>

                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h3 class="text-lg font-semibold">📘 Computer Networking: A Top-Down Approach – Kurose & Ross</h3>
                        <p class="text-sm text-gray-700">Widely recommended for understanding layers.</p>
                    </div>

                    <!-- GK & Competitive Books -->
                    <h2 class="text-xl font-bold text-indigo-700 mt-10">📖 General Knowledge & Other Subjects</h2>

                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h3 class="text-lg font-semibold">🧠 Lucent's General Knowledge</h3>
                        <p class="text-sm text-gray-700">Most popular book for competitive exam GK.</p>
                    </div>

                    <div class="p-4 bg-pink-50 border border-pink-200 rounded-lg">
                        <h3 class="text-lg font-semibold">🔬 General Science – Arihant</h3>
                        <p class="text-sm text-gray-700">Science guide for SSC, UPSC & other exams.</p>
                    </div>

                    <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <h3 class="text-lg font-semibold">📜 Indian History – Spectrum</h3>
                        <p class="text-sm text-gray-700">Highly recommended for Civil Services & SSC.</p>
                    </div>

                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h3 class="text-lg font-semibold">🌍 Geography of India – Majid Husain</h3>
                        <p class="text-sm text-gray-700">Standard book for UPSC and State PCS.</p>
                    </div>

                    <!-- Reasoning, Maths & English Grammar -->
                    <h2 class="text-xl font-bold text-indigo-700 mt-10">📖 Reasoning, Maths & English Grammar</h2>

                    <div class="p-4 bg-sky-50 border border-sky-200 rounded-lg">
                        <h3 class="text-lg font-semibold">🧩 A Modern Approach to Verbal & Non-Verbal Reasoning – R.S. Aggarwal</h3>
                        <p class="text-sm text-gray-700">Most trusted book for all types of reasoning questions.</p>
                    </div>

                    <div class="p-4 bg-sky-50 border border-sky-200 rounded-lg">
                        <h3 class="text-lg font-semibold">🧠 Analytical Reasoning – M.K. Pandey</h3>
                        <p class="text-sm text-gray-700">Good for conceptual clarity in analytical reasoning.</p>
                    </div>

                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <h3 class="text-lg font-semibold">📘 Quantitative Aptitude – R.S. Aggarwal</h3>
                        <p class="text-sm text-gray-700">Comprehensive book for arithmetic, algebra, geometry & data interpretation.</p>
                    </div>

                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                        <h3 class="text-lg font-semibold">📏 Fast Track Objective Arithmetic – Rajesh Verma</h3>
                        <p class="text-sm text-gray-700">Ideal for practice and shortcut techniques in competitive math.</p>
                    </div>

                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg">
                        <h3 class="text-lg font-semibold">📖 High School English Grammar & Composition – Wren & Martin</h3>
                        <p class="text-sm text-gray-700">Classic book to strengthen grammar basics.</p>
                    </div>

                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg">
                        <h3 class="text-lg font-semibold">✍️ Objective General English – S.P. Bakshi</h3>
                        <p class="text-sm text-gray-700">Covers vocabulary, grammar rules, and previous year questions.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-pink-500 to-indigo-500 p-6 rounded-lg shadow-lg text-center flex justify-center items-center space-x-3">
            <h2 class="text-4xl font-bold text-white tracking-wide animate-pulse flex items-center">
                <span class="animate-bounce">📜</span> <!-- Bouncing Paper Icon -->
                <span class="ml-2">Paper Setter Dashboard</span>
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-purple-600 via-blue-500 to-pink-500 flex justify-center items-center">
        <div class="max-w-7xl w-full mx-auto px-6 mt-[-160px]">  
            <div class="bg-white shadow-2xl rounded-3xl p-10 border border-gray-300 backdrop-blur-lg 
                        bg-opacity-90 transition-all duration-500 hover:shadow-2xl flex flex-col justify-center items-center">
                
                <h1 class="text-4xl font-bold text-center text-gray-800 drop-shadow-lg">
                    Welcome, Paper Setter 🎉
                </h1>

                <!-- Centered Label -->
                <p class="text-lg text-gray-600 text-center my-6 max-w-2xl">
                    Manage exam questions and papers effortlessly.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full">
                    <!-- Manage Questions -->
                    <a href="{{ route('paper_setter.questions.index') }}" 
                       class="group relative block bg-white shadow-md border border-blue-500 rounded-xl p-8 
                              transition-all duration-300 transform hover:-translate-y-5 hover:scale-110 
                              hover:shadow-[0_0_40px_#3b82f6]">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-500 text-white p-4 rounded-lg shadow-lg 
                                        group-hover:scale-110 transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16M4 9h16M4 15h16M4 20h5"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">Manage Questions</h3>
                                <p class="text-sm text-gray-500">Create and update questions.</p>
                            </div>
                        </div>
                    </a>

                    <!-- Manage Exam Papers -->
                    <a href="{{ route('paper_setter.exams.index') }}" 
                       class="group relative block bg-white shadow-md border border-green-500 rounded-xl p-8 
                              transition-all duration-300 transform hover:-translate-y-5 hover:scale-110 
                              hover:shadow-[0_0_40px_#22c55e]">
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-500 text-white p-4 rounded-lg shadow-lg 
                                        group-hover:scale-110 transition-all">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v4h6v-4m-6 0h6m-6-8h6M3 9h18"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">Manage Descriptive Questions</h3>
                                <p class="text-sm text-gray-500">Organize exam papers.</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

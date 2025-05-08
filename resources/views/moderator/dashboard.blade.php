<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-blue-500 via-purple-600 to-indigo-600 p-6 rounded-md shadow-lg transition duration-300 hover:opacity-90 flex justify-center items-center space-x-3">
            <span class="text-3xl animate-bounce">🛡️</span> <!-- Bouncing Shield Icon -->
            <h2 class="font-semibold text-2xl text-white leading-tight text-center tracking-wide animate-pulse">
                Moderator Dashboard
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 bg-opacity-90 backdrop-blur-lg overflow-hidden shadow-2xl sm:rounded-lg p-8 transition-all duration-300 hover:shadow-3xl">
                
                <h1 class="text-4xl font-extrabold mb-4 text-gray-900 dark:text-gray-100 text-center tracking-wide animate-fade-in">
                    ✨ Welcome, Moderator! ✨
                </h1>
                
                <h3 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-300 text-center">Your Control Panel</h3>
                <p class="mb-6 text-gray-600 dark:text-gray-400 text-center">
                    Manage exams, paper setters, and moderators efficiently!
                </p>

                <!-- Dashboard Stats (Static Values) -->
                <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Total Exams Card -->
                    <div class="bg-white dark:bg-gray-700 bg-opacity-80 backdrop-blur-lg p-6 shadow-xl rounded-lg flex items-center space-x-4 hover:scale-110 transition-transform duration-500 cursor-pointer transform hover:-translate-y-2 animate-fade-in">
                        <div class="text-green-500 text-6xl">📝</div>
                        <div>
                            <h3 class="text-xl font-bold">Total Exams</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-lg">
                                45 Scheduled
                            </p>
                        </div>
                    </div>

                    <!-- Paper Setters Card -->
                    <div class="bg-white dark:bg-gray-700 bg-opacity-80 backdrop-blur-lg p-6 shadow-xl rounded-lg flex items-center space-x-4 hover:scale-110 transition-transform duration-500 cursor-pointer transform hover:-translate-y-2 animate-fade-in">
                        <div class="text-blue-500 text-6xl">📄</div>
                        <div>
                            <h3 class="text-xl font-bold">Paper Setters</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-lg">
                                15 Active
                            </p>
                        </div>
                    </div>

                    <!-- Moderators Online Card -->
                    <div class="bg-white dark:bg-gray-700 bg-opacity-80 backdrop-blur-lg p-6 shadow-xl rounded-lg flex items-center space-x-4 hover:scale-110 transition-transform duration-500 cursor-pointer transform hover:-translate-y-2 animate-fade-in">
                        <div class="text-yellow-500 text-6xl">👥</div>
                        <div>
                            <h3 class="text-xl font-bold">Moderators</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-lg">
                                3 Online
                            </p>
                        </div>
                    </div>

                </section>

                <!-- Quick Actions -->
                <section class="mt-10">
                    <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100 text-center">🚀 Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center">
                        <a href="{{ route('moderator.exams.index') }}" 
                           class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-md shadow-lg transform hover:scale-110 transition duration-300 text-lg font-semibold tracking-wide">
                            ✏️ Manage Exams
                        </a>
                        <a href="{{ route('moderator.paper_setters.index') }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-md shadow-lg transform hover:scale-110 transition duration-300 text-lg font-semibold tracking-wide">
                            📜 Manage Paper Setters
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>

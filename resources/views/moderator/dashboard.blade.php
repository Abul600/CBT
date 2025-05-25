<x-app-layout>
    <div class="pt-0 pb-12 bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 dark:from-gray-900 dark:via-gray-800 dark:to-gray-700">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Moved Moderator Dashboard header inside -->
            <div class="bg-gradient-to-r from-blue-500 via-purple-600 to-indigo-600 p-4 rounded-md shadow-lg transition duration-300 hover:opacity-90 flex justify-center items-center space-x-3 mt-6 mb-10">
                <span class="text-3xl animate-bounce">🛡️</span>
                <h2 class="font-semibold text-2xl text-white leading-tight text-center tracking-wide animate-pulse">
                    Moderator Dashboard
                </h2>
            </div>

            <h1 class="text-4xl font-extrabold mb-4 text-gray-900 dark:text-gray-100 text-center tracking-wide animate-fade-in">
                ✨ Welcome, Moderator! ✨
            </h1>

            <h3 class="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-300 text-center">Your Control Panel</h3>
            <p class="mb-6 text-gray-600 dark:text-gray-400 text-center">
                Manage exams, paper setters, and moderators efficiently!
            </p>

            <!-- Dashboard Stats -->
            <section class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Total Exams -->
                <div class="bg-white dark:bg-gray-700 border-2 border-green-500 p-6 shadow-md rounded-lg flex items-center space-x-4 hover:scale-105 transform transition duration-300">
                    <div class="text-green-500 text-5xl">📝</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Total Exams</h3>
                        <p class="text-gray-600 dark:text-gray-400">45 Scheduled</p>
                    </div>
                </div>

                <!-- Paper Setters -->
                <div class="bg-white dark:bg-gray-700 border-2 border-blue-500 p-6 shadow-md rounded-lg flex items-center space-x-4 hover:scale-105 transform transition duration-300">
                    <div class="text-blue-500 text-5xl">📄</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Paper Setters</h3>
                        <p class="text-gray-600 dark:text-gray-400">15 Active</p>
                    </div>
                </div>

                <!-- Moderators -->
                <div class="bg-white dark:bg-gray-700 border-2 border-yellow-500 p-6 shadow-md rounded-lg flex items-center space-x-4 hover:scale-105 transform transition duration-300">
                    <div class="text-yellow-500 text-5xl">👥</div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Moderators</h3>
                        <p class="text-gray-600 dark:text-gray-400">3 Online</p>
                    </div>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="mt-10">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100 text-center">🚀 Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 text-center">
                    <a href="{{ route('moderator.exams.index') }}"
                       class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-md shadow-lg transform hover:scale-105 origin-center transition duration-300 text-lg font-semibold tracking-wide">
                        ✏️ Manage Exams
                    </a>
                    <a href="{{ route('moderator.paper_setters.index') }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-md shadow-lg transform hover:scale-105 origin-center transition duration-300 text-lg font-semibold tracking-wide">
                        📜 Manage Paper Setters
                    </a>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        neon: '#00ffff',
                        magenta: '#ff00ff',
                        emerald: '#10b981',
                        overlay: 'rgba(0,0,0,0.6)'
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                    }
                }
            }
        }
    </script>
</head>
<body 
    x-data="{ 
        darkMode: true,  
        open: false,
        confirmLogout: false,
        toggleDark() {
            localStorage.setItem('darkMode', this.darkMode);
        }
    }"
    x-init="$watch('darkMode', value => document.documentElement.classList.toggle('dark', value))"
    class="min-h-screen bg-gradient-to-br from-blue-100 via-sky-100 to-white dark:from-indigo-900 dark:via-violet-900 dark:to-fuchsia-900 text-gray-900 dark:text-gray-100 transition-colors duration-500"
>

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-gradient-to-r from-sky-600 via-indigo-600 to-purple-600 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="{{ route('student.dashboard') }}" class="text-2xl font-extrabold tracking-wide flex items-center space-x-2">
                <span>🚀</span><span>Student Portal</span>
            </a>

            <div class="hidden md:flex items-center space-x-6">
                <a href="#" class="relative group">
                    <span class="group-hover:text-neon transition duration-300">👤 Profile</span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-neon group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#" class="relative group">
                    <span class="group-hover:text-neon transition duration-300">📢 Notices</span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-neon group-hover:w-full transition-all duration-300"></span>
                </a>
                <a href="#" class="relative group">
                    <span class="group-hover:text-neon transition duration-300">💬 Support</span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-neon group-hover:w-full transition-all duration-300"></span>
                </a>

                <!-- Logout Button -->
                <button @click="confirmLogout = true" class="bg-emerald-500 hover:bg-emerald-600 px-4 py-2 rounded text-white font-semibold shadow-md hover:shadow-lg transition">
                    Logout
                </button>
            </div>

            <!-- Mobile menu toggle -->
            <div class="md:hidden">
                <button @click="open = !open">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="w-6 h-6" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-transition class="md:hidden px-6 pb-4 bg-indigo-700 space-y-2">
            <a href="#" class="block text-white hover:text-neon">👤 Profile</a>
            <a href="#" class="block text-white hover:text-neon">📢 Notices</a>
            <a href="#" class="block text-white hover:text-neon">💬 Support</a>
            <button @click="confirmLogout = true" class="text-red-200 hover:text-white">Logout</button>
        </div>
    </nav>

    <!-- Logout Modal -->
    <div x-show="confirmLogout" x-transition class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg text-center w-full max-w-sm">
            <h2 class="text-xl font-semibold mb-4">Logout Confirmation</h2>
            <p class="mb-6">Are you sure you want to logout?</p>
            <form action="{{ route('logout') }}" method="POST" class="flex justify-center gap-4">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow-md">Yes</button>
                <button type="button" @click="confirmLogout = false" class="border px-4 py-2 rounded dark:border-gray-500">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto mt-6 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

</body>
</html>

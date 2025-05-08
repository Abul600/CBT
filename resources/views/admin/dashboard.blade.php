<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-500 p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Admin User') }}
            </h2>
        </div>
    </x-slot>

    <div class="relative min-h-screen bg-cover bg-center flex flex-col justify-center items-center overflow-hidden"
         style="background-image: url('{{ asset('images/13.webp') }}');">

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.moderators.index') }}" 
               class="absolute top-20 px-10 py-4 text-xl font-bold w-64 text-center bg-red-500 text-black rounded-lg shadow-md 
                      hover:bg-gradient-to-r hover:from-yellow-400 hover:to-red-500 transition-all duration-300 ease-in-out 
                      hover:scale-110 border-4 border-green-400 ring-8 ring-yellow-500 ring-opacity-75 
                      hover:shadow-[0px_0px_20px_rgba(255,255,255,0.8)]">
                Manage Moderators
            </a>
        @endif

        <!-- Moderator Instructions Card (Moved Upwards) -->
        <div class="mt-10 w-2/4 p-6 rounded-lg shadow-lg bg-white/10 backdrop-blur-sm transition-all duration-300 ease-in-out 
                    hover:scale-105 hover:shadow-[0px_0px_20px_rgba(255,255,255,0.5)]">
            <h3 class="text-3xl font-extrabold text-transparent bg-clip-text 
                       bg-gradient-to-r from-yellow-400 via-red-500 to-purple-500 
                       animate-gradient-slow mb-4 drop-shadow-lg text-center">
                Moderator Guidelines
            </h3>
            <ul class="list-disc list-inside text-white drop-shadow-lg text-lg space-y-2 marker:text-yellow-400">
                <li class="hover:text-yellow-400 hover:translate-x-2 transition-transform duration-300 ease-in-out">
                    <strong>Manage Paper Setters:</strong> Add up to 3 Paper Setters and assign exam question duties.
                </li>
                <li class="hover:text-yellow-400 hover:translate-x-2 transition-transform duration-300 ease-in-out">
                    <strong>Manage Paper Setters:</strong> Assign Paper Setters to exams as needed.
                </li>
                <li class="hover:text-yellow-400 hover:translate-x-2 transition-transform duration-300 ease-in-out">
                    <strong>Review and Approve Questions:</strong> Approve, modify, or reject questions before finalizing.
                </li>
                <li class="hover:text-yellow-400 hover:translate-x-2 transition-transform duration-300 ease-in-out">
                    <strong>Create Exam Papers:</strong> Organize reviewed questions into structured exam papers.
                </li>
                <li class="hover:text-yellow-400 hover:translate-x-2 transition-transform duration-300 ease-in-out">
                    <strong>Manage Exams:</strong> Assign exam slots, monitor progress, and ensure fair conduct.
                </li>
                <li class="hover:text-yellow-400 hover:translate-x-2 transition-transform duration-300 ease-in-out">
                    <strong>Monitor Paper Setters :</strong> Ensure compliance and report issues to Admin.
                </li>
                <li class="hover:text-yellow-400 hover:translate-x-2 transition-transform duration-300 ease-in-out">
                    <strong>Follow Platform Rules:</strong> Maintain exam integrity and adhere to platform policies.
                </li>
            </ul>
        </div>
    </div>
</x-app-layout>

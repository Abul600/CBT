<x-app-layout>
    <x-slot name="header">
        <div class="bg-blue-500 p-4 rounded-md">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Admin User') }}
            </h2>
        </div>
    </x-slot>

    <div class="min-h-screen bg-cover bg-center flex justify-center items-center" 
         style="background-image: url('{{ asset('images/666.avif') }}');">
        
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.moderators.index') }}" 
               class="px-10 py-4 text-xl font-bold w-64 text-center bg-red-500 text-black rounded-lg shadow-md 
                      hover:bg-blue-600 mt-[-560px] border-4 border-green-400  ring-8 ring-yellow-500 ring-opacity-75">
                Manage Moderators
            </a>
        @endif

    </div>
</x-app-layout>

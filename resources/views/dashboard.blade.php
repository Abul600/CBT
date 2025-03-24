@php
    use Spatie\Permission\Models\Role;

    $user = auth()->user();
@endphp

{{-- Redirect users based on their Spatie role --}}
@if($user->hasRole('admin'))
    @include('admin.dashboard')
@elseif($user->hasRole('moderator'))
    @include('moderator.dashboard')
@elseif($user->hasRole('student'))
    @include('student.dashboard')
@elseif($user->hasRole('paper_seater'))
    @include('paper_seater.dashboard')
@else
    {{-- Default fallback for unassigned roles --}}
    <x-app-layout>
        <x-slot name="header">
            <div class="bg-gray-800 p-4 rounded-md">
                <h2 class="font-semibold text-xl text-white leading-tight">
                    {{ __('Dashboard') }}
                </h2>
            </div>
        </x-slot>

        <div class="min-h-screen flex items-center justify-center bg-gray-200">
            <h1 class="text-2xl font-bold">Welcome to the System</h1>
        </div>
    </x-app-layout>
@endif

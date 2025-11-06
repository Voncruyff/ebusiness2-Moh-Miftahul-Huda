<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                    <h1 class="text-3xl font-bold mb-4">Selamat Datang di Dashboard 🎉</h1>
                    <p class="text-gray-600 dark:text-gray-300">Halo, {{ Auth::user()->name }}! Kamu berhasil login.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

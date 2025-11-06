<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">Halo, {{ Auth::user()->name }} 👋</h1>
                <p>Selamat datang di halaman <strong>Admin Dashboard</strong>.</p>
                <p class="mt-2">Role Anda: <span class="font-semibold text-red-600">{{ Auth::user()->role }}</span></p>

                <hr class="my-4">

                <div class="mt-4">
                    <p class="text-gray-700">Di sini nantinya Anda bisa mengatur data user, produk, atau fitur lain sesuai kebutuhan e-business Anda.</p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-700 underline">
                        Kembali ke Dashboard User
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

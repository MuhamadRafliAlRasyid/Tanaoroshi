@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Dashboard Admin</h1>
            <p class="text-gray-600 text-lg">Selamat datang, <span
                    class="font-medium text-blue-600">{{ Auth::user()->name }}</span>. Ini adalah dashboard Admin.</p>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('admin.index') }}"
                    class="bg-blue-100 text-blue-600 font-semibold text-center py-4 rounded-lg hover:bg-blue-200 transition">
                    <i class="fas fa-users mr-2"></i> Kelola User
                </a>
                <a href="#"
                    class="bg-green-100 text-green-600 font-semibold text-center py-4 rounded-lg hover:bg-green-200 transition">
                    <i class="fas fa-chart-bar mr-2"></i> Laporan
                </a>
            </div>
        </section>
    </main>
@endsection

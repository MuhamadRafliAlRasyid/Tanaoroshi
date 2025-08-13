@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section class="w-full max-w-4xl bg-white rounded-lg shadow-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-2">Edit User</h1>
            <form action="{{ route('admin.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                @csrf
                @method('PUT')
                @include('admin.form')

                <div class="col-span-full flex items-center gap-4 mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                    <a href="{{ route('admin.index') }}" class="text-gray-600 font-semibold hover:text-blue-600 transition">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </section>
    </main>
@endsection

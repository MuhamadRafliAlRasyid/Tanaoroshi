@extends('layouts.app')

@section('content')
    <h1>Edit User</h1>
    <form action="{{ route('admin.update', $user->id) }}" method="POST" enctype="multipart/form-data"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs text-gray-700">
        @csrf
        @method('PUT')
        @include('admin.form')

        <div class="sm:col-span-2 md:col-span-3 flex items-center gap-4 mt-2">
            <button type="submit"
                class="bg-blue-600 text-white text-xs font-semibold rounded px-4 py-2 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Update
            </button>
            <a href="{{ route('admin.index') }}"
                class="text-blue-600 text-xs font-semibold hover:underline focus:outline-none">
                Batal
            </a>
        </div>
    </form>
@endsection

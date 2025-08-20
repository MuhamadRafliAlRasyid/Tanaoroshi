@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <main class="p-6 flex flex-col items-center space-y-8">
        <section
            class="w-full max-w-4xl bg-white rounded-xl shadow-2xl p-6 transform transition-all duration-300 hover:shadow-3xl">
            <h1 class="text-2xl font-semibold text-indigo-800 mb-6 border-b-2 border-indigo-200 pb-3 flex items-center">
                <i class="fas fa-edit mr-2 text-indigo-600"></i> Edit User
            </h1>

            <form action="{{ route('admin.update', $user->id) }}" method="POST" enctype="multipart/form-data"
                class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700">
                @csrf
                @method('PUT')

                <!-- Foto Profil -->
                <div class="col-span-full flex items-center gap-6 mb-6">
                    <img id="current_photo" alt="User profile"
                        class="w-20 h-20 rounded-lg object-cover border-2 border-gray-200"
                        src="{{ $user->profile_photo_path ? asset('img/profile_photo/' . $user->profile_photo_path) : asset('images/avatar.png') }}" />
                    <label for="profile_photo"
                        class="flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 cursor-pointer transition">
                        <i class="fas fa-upload"></i> Ganti Foto
                    </label>
                    <input id="profile_photo" name="profile_photo" type="file" class="hidden" accept="image/*">
                </div>

                <!-- Nama -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input id="name" name="name" type="text" required value="{{ old('name', $user->name) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email', $user->email) }}"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>

                <!-- Password -->
                <div x-data="{ showPassword: false }" class="relative">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password (Kosongkan jika
                        tidak diubah)</label>
                    <div class="relative">
                        <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10"
                            placeholder="Masukkan password baru" />
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                </div>

                @if (auth()->user()->role === 'admin')
                    <!-- Bagian -->
                    <div>
                        <label for="bagian_id" class="block text-sm font-medium text-gray-700 mb-1">Bagian</label>
                        <select id="bagian_id" name="bagian_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">- Pilih Bagian -</option>
                            @foreach ($bagians as $bagian)
                                <option value="{{ $bagian->id }}"
                                    {{ old('bagian_id', $user->bagian_id) == $bagian->id ? 'selected' : '' }}>
                                    {{ $bagian->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                        <select id="role" name="role" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach (['admin', 'karyawan', 'super'] as $role)
                                <option value="{{ $role }}"
                                    {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-span-full flex items-center gap-4 mt-6">
                    <button type="submit"
                        class="bg-blue-600 text-white font-semibold px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition flex items-center">
                        <i class="fas fa-save mr-2"></i> Update
                    </button>
                    <a href="{{ route('admin.index') }}"
                        class="text-gray-600 font-semibold hover:text-blue-600 transition flex items-center">
                        <i class="fas fa-times mr-2"></i> Batal
                    </a>
                </div>
            </form>
        </section>
    </main>

    <script>
        document.getElementById('profile_photo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('current_photo');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection

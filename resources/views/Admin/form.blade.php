<!-- Profile Information -->
<section aria-label="Profile Information" class="border border-gray-300 rounded-lg p-6 max-w-4xl w-full">
    <h2 class="font-semibold text-gray-800 mb-4">Profile Information</h2>

    <div class="flex items-center gap-4 mb-4">
        <img id="current_photo" alt="User profile" class="w-16 h-16 rounded-md object-cover"
            src="{{ isset($user) && $user->profile_photo_path ? asset('img/profile_photo/' . $user->profile_photo_path) : asset('assets/avatar.png') }}" />

        <label for="profile_photo"
            class="cursor-pointer flex items-center gap-1 border border-gray-300 rounded-md px-3 py-1 text-xs text-gray-700 hover:bg-gray-100">
            Ganti Foto
            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </label>
        <input id="profile_photo" name="profile_photo" type="file" class="hidden" accept="image/*">
    </div>

    <!-- Name -->
    <div>
        <label class="block font-semibold mb-1" for="name">Nama</label>
        <input id="name" name="name" type="text" required value="{{ old('name', $user->name ?? '') }}"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
    </div>

    <!-- Email -->
    <div>
        <label class="block font-semibold mb-1" for="email">Email</label>
        <input id="email" name="email" type="email" required value="{{ old('email', $user->email ?? '') }}"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
    </div>

    <!-- Password -->
    <div>
        <label class="block font-semibold mb-1" for="password">
            Password {{ isset($user) ? '(Kosongkan jika tidak diubah)' : '' }}
        </label>
        <input id="password" name="password" type="password"
            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
    </div>

    @if (auth()->user()->role === 'admin')
        <!-- Bagian (Hanya admin yang bisa lihat dan ubah) -->
        <div>
            <label class="block font-semibold mb-1" for="bagian_id">Bagian</label>
            <select id="bagian_id" name="bagian_id"
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="">- Pilih Bagian -</option>
                @foreach ($bagians as $bagian)
                    <option value="{{ $bagian->id }}"
                        {{ old('bagian_id', $user->bagian_id ?? '') == $bagian->id ? 'selected' : '' }}>
                        {{ $bagian->nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Role (Hanya admin yang bisa ubah) -->
        <div>
            <label class="block font-semibold mb-1" for="role">Role</label>
            <select id="role" name="role" required
                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                @foreach (['admin', 'user', 'spv'] as $role)
                    <option value="{{ $role }}" {{ old('role', $user->role ?? '') == $role ? 'selected' : '' }}>
                        {{ ucfirst($role) }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</section>

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

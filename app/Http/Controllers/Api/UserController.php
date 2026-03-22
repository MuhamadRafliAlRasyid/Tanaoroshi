<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index(Request $request)
    {$query = User::with('bagian')->where('role', '!=', 'super');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('bagian', fn($q) => $q->where('nama_bagian', 'like', "%{$search}%"));
            });
        }

        $users = $query->latest()->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,karyawan',
            'bagian_id' => 'nullable|exists:bagian,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'bagian_id' => $request->bagian_id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User berhasil ditambahkan',
            'data' => $user->load('bagian')
        ], 201);
    }

    public function show(User $user)
    {
        return response()->json([
            'status' => true,
            'data' => $user->load('bagian')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,karyawan',
            'bagian_id' => 'nullable|exists:bagian,id',
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update($request->only(['name', 'email', 'role', 'bagian_id']));

        return response()->json([
            'status' => true,
            'message' => 'User berhasil diperbarui',
            'data' => $user->fresh('bagian')
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => 'User berhasil dihapus'
        ]);
    }
}

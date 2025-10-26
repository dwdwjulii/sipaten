<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password; 

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        //  dd($request->all());
        $search = $request->input('search');

        // query ke model User
        $query = User::query();

        $query->when($search, function ($q, $search) {
            return $q->where('name', 'like', '%' . $search . '%')
                     ->orWhere('email', 'like', '%' . $search . '%');
        });

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('pengguna', ['users' => $users]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()], 
            'role' => ['required', 'in:admin,petugas'],
            'status' => ['required', 'in:aktif,non-aktif'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:admin,petugas',
            'status' => 'required|in:aktif,non-aktif',
            'password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'status' => $validatedData['status'],
        ];

        if (!empty($validatedData['password'])) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($updateData);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        
        // Hapus user dari database
        $user->delete();

        // Redirect balik dengan pesan sukses
        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil dihapus!');
    }

}

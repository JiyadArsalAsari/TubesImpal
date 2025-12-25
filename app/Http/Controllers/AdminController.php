<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        
        // Simple stats
        $latestUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalUsers', 'totalMahasiswa', 'totalDosen', 'latestUsers'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:mahasiswa,dosen',
            'is_admin' => 'nullable|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_admin' => $request->boolean('is_admin'),
        ]);

        if ($request->role == 'mahasiswa') {
            Mahasiswa::create([
                'mahasiswaID' => 'MHS' . time(), // Simple ID generation
                'nama' => $request->name,
                'user_id' => $user->id,
            ]);
        } elseif ($request->role == 'dosen') {
            Dosen::create([
                'dosenID' => 'DSN' . time(),
                'nama' => $request->name,
                'user_id' => $user->id,
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_admin' => 'nullable|boolean',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->is_admin = $request->boolean('is_admin');
        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting self
        if (auth()->id() == $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Cleanup related data
        if ($user->role == 'mahasiswa') {
            $user->mahasiswa()->delete();
        } elseif ($user->role == 'dosen') {
            $user->dosen()->delete();
        }
        
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}

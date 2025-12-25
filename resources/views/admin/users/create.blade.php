@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users') }}" class="text-gray-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold">Create New User</h1>
    </div>

    <div class="card-item p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                <input type="text" name="name" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                <input type="text" name="username" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                <select name="role" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500">
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_admin" id="is_admin" value="1" class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                <label for="is_admin" class="text-sm font-medium text-gray-300">Berikan akses Administrator</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" name="password" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
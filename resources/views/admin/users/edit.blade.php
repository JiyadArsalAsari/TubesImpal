@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users') }}" class="text-gray-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold">Edit User</h1>
    </div>

    <div class="card-item p-6">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                <input type="text" name="name" value="{{ $user->name }}" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ $user->email }}" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                <input type="text" value="{{ $user->username }}" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-gray-400 cursor-not-allowed" disabled>
                <p class="text-xs text-gray-500 mt-1">Username cannot be changed.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                <input type="text" value="{{ ucfirst($user->role) }}" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-gray-400 cursor-not-allowed" disabled>
                <p class="text-xs text-gray-500 mt-1">Role cannot be changed.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_admin" id="is_admin" value="1" class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2" {{ $user->is_admin ? 'checked' : '' }}>
                <label for="is_admin" class="text-sm font-medium text-gray-300">Akses Administrator</label>
            </div>

            <div class="border-t border-gray-600 pt-4 mt-4">
                <h3 class="text-lg font-semibold mb-4">Change Password (Optional)</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                        <input type="password" name="password" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
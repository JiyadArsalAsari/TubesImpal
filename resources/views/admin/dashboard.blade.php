@extends('layouts.app')

@section('content')
<div class="mb-6">
    <button onclick="window.location.href='{{ route('admin.dashboard') }}'" class="flex items-center gap-2 text-white hover:text-gray-300 transition-colors">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Back to Dashboard</span>
    </button>
</div>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">Admin Dashboard <span class="ml-2 px-2 py-1 text-xs rounded bg-red-600">Administrator</span></h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-blue-600 rounded-lg hover:bg-blue-700 transition">
                Manage Users
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-item p-6">
            <h3 class="text-gray-300 mb-2">Total Users</h3>
            <p class="text-4xl font-bold">{{ $totalUsers }}</p>
        </div>
        <div class="card-item p-6">
            <h3 class="text-gray-300 mb-2">Total Mahasiswa</h3>
            <p class="text-4xl font-bold">{{ $totalMahasiswa }}</p>
        </div>
        <div class="card-item p-6">
            <h3 class="text-gray-300 mb-2">Total Dosen</h3>
            <p class="text-4xl font-bold">{{ $totalDosen }}</p>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card-item p-6">
        <h2 class="text-xl font-semibold mb-4">Latest Registered Users</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="pb-3">Name</th>
                        <th class="pb-3">Role</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3">Joined</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($latestUsers as $user)
                    <tr class="border-b border-gray-700 last:border-0">
                        <td class="py-3">{{ $user->name }}</td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $user->role == 'dosen' ? 'bg-green-500' : 'bg-blue-500' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            @if($user->is_admin)
                                <span class="ml-2 px-2 py-1 rounded text-xs bg-red-500">Administrator</span>
                            @endif
                        </td>
                        <td class="py-3">{{ $user->email }}</td>
                        <td class="py-3">{{ $user->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
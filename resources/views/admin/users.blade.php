@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold">User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-green-600 rounded-lg hover:bg-green-700 transition">
            <i class="fa-solid fa-plus mr-2"></i>Add User
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-600 bg-opacity-20 border border-green-500 text-green-100 p-4 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="card-item p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="pb-3">Name</th>
                        <th class="pb-3">Role</th>
                        <th class="pb-3">Email</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($users as $user)
                    <tr class="border-b border-gray-700 last:border-0">
                        <td class="py-3">
                            <div class="font-semibold">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $user->username }}</div>
                        </td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded text-xs {{ $user->role == 'dosen' ? 'bg-green-500' : 'bg-blue-500' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                            @if($user->is_admin)
                                <span class="ml-2 px-2 py-1 rounded text-xs bg-red-500">Administrator</span>
                            @endif
                        </td>
                        <td class="py-3">{{ $user->email }}</td>
                        <td class="py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="p-2 bg-blue-600 rounded hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-600 rounded hover:bg-red-700 transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
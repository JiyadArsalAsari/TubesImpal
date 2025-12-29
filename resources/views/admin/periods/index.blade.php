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
        <h1 class="text-2xl font-bold">Academic Periods</h1>
        <a href="{{ route('admin.periods.create') }}" class="px-4 py-2 bg-green-600 rounded-lg hover:bg-green-700 transition">
            <i class="fa-solid fa-plus mr-2"></i>Add Period
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-600 bg-opacity-20 border border-green-500 text-green-100 p-4 rounded-lg">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-600 bg-opacity-20 border border-red-500 text-red-100 p-4 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="card-item p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="pb-3">Name</th>
                        <th class="pb-3">Start Date</th>
                        <th class="pb-3">End Date</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Active</th>
                        <th class="pb-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    @foreach($periods as $period)
                    <tr class="border-b border-gray-700 last:border-0">
                        <td class="py-3 font-semibold">{{ $period->name }}</td>
                        <td class="py-3">{{ $period->start_date->format('d M Y') }}</td>
                        <td class="py-3">{{ $period->end_date->format('d M Y') }}</td>
                        <td class="py-3">
                            <span class="px-2 py-1 rounded text-xs 
                                {{ $period->status == 'active' ? 'bg-green-500' : 
                                   ($period->status == 'setup' ? 'bg-yellow-500' : 
                                   ($period->status == 'closed' ? 'bg-red-500' : 'bg-blue-500')) }}">
                                {{ ucfirst($period->status) }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($period->is_active)
                                <span class="text-green-400 font-bold"><i class="fa-solid fa-check-circle mr-1"></i>Active</span>
                            @else
                                <form action="{{ route('admin.periods.activate', $period->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-green-400 transition text-sm">
                                        Set Active
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.periods.edit', $period->id) }}" class="p-2 bg-blue-600 rounded hover:bg-blue-700 transition">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                @if(!$period->is_active)
                                <form action="{{ route('admin.periods.destroy', $period->id) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
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
            {{ $periods->links() }}
        </div>
    </div>
</div>
@endsection
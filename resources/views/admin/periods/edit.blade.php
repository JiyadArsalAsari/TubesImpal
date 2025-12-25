@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.periods.index') }}" class="text-gray-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold">Edit Academic Period</h1>
    </div>

    <div class="card-item p-6">
        <form action="{{ route('admin.periods.update', $period->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Period Name</label>
                <input type="text" name="name" value="{{ $period->name }}" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ $period->start_date->format('Y-m-d') }}" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ $period->end_date->format('Y-m-d') }}" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500">
                    <option value="setup" {{ $period->status == 'setup' ? 'selected' : '' }}>Setup (Preparation)</option>
                    <option value="krs" {{ $period->status == 'krs' ? 'selected' : '' }}>KRS (Course Selection)</option>
                    <option value="active" {{ $period->status == 'active' ? 'selected' : '' }}>Active (Ongoing)</option>
                    <option value="grading" {{ $period->status == 'grading' ? 'selected' : '' }}>Grading (Final Exams/Scores)</option>
                    <option value="closed" {{ $period->status == 'closed' ? 'selected' : '' }}>Closed (Archived)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $period->is_active ? 'checked' : '' }} class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                <label for="is_active" class="text-sm font-medium text-gray-300">Set as Current Active Period</label>
            </div>
            @if(!$period->is_active)
            <p class="text-xs text-yellow-500 ml-6 mb-4">Warning: Activating this period will deactivate any other currently active period.</p>
            @endif

            <div class="pt-4">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
                    Update Period
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
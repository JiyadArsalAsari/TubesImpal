@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.periods.index') }}" class="text-gray-400 hover:text-white transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold">Create Academic Period</h1>
    </div>

    <div class="card-item p-6">
        <form action="{{ route('admin.periods.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Period Name</label>
                <input type="text" name="name" placeholder="e.g. Ganjil 2024/2025" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">End Date</label>
                    <input type="date" name="end_date" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                <select name="status" class="w-full bg-[#1f2f1f] border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-green-500">
                    <option value="setup">Setup (Preparation)</option>
                    <option value="krs">KRS (Course Selection)</option>
                    <option value="active">Active (Ongoing)</option>
                    <option value="grading">Grading (Final Exams/Scores)</option>
                    <option value="closed">Closed (Archived)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2">
                <label for="is_active" class="text-sm font-medium text-gray-300">Set as Current Active Period</label>
            </div>
            <p class="text-xs text-yellow-500 ml-6 mb-4">Warning: Activating this period will deactivate any other currently active period.</p>

            <div class="pt-4">
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                    Create Period
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
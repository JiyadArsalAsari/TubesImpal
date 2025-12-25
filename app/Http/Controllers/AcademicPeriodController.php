<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class AcademicPeriodController extends Controller
{
    public function index()
    {
        $periods = AcademicPeriod::latest()->paginate(10);
        return view('admin.periods.index', compact('periods'));
    }

    public function create()
    {
        return view('admin.periods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:setup,krs,active,grading,closed',
        ]);

        $data = $request->all();
        
        // If this period is set to active, deactivate others
        if ($request->has('is_active') && $request->is_active) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
            $data['is_active'] = true;
        } else {
            $data['is_active'] = false;
        }

        AcademicPeriod::create($data);

        return redirect()->route('admin.periods.index')->with('success', 'Academic Period created successfully.');
    }

    public function edit($id)
    {
        $period = AcademicPeriod::findOrFail($id);
        return view('admin.periods.edit', compact('period'));
    }

    public function update(Request $request, $id)
    {
        $period = AcademicPeriod::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:setup,krs,active,grading,closed',
        ]);

        $data = $request->all();

        // Handle active status switch
        if ($request->has('is_active') && $request->is_active) {
            // If we are activating this period, deactivate all others
            AcademicPeriod::where('id', '!=', $id)->update(['is_active' => false]);
            $data['is_active'] = true;
        } else {
            // Check if we are deactivating the currently active period
            // (Optional logic: prevent deactivating if it's the only one, but let's allow it for now)
            $data['is_active'] = false;
        }

        $period->update($data);

        return redirect()->route('admin.periods.index')->with('success', 'Academic Period updated successfully.');
    }

    public function destroy($id)
    {
        $period = AcademicPeriod::findOrFail($id);
        
        if ($period->is_active) {
            return redirect()->back()->with('error', 'Cannot delete an active academic period.');
        }

        $period->delete();

        return redirect()->route('admin.periods.index')->with('success', 'Academic Period deleted successfully.');
    }

    public function activate($id)
    {
        $period = AcademicPeriod::findOrFail($id);
        
        AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        
        $period->is_active = true;
        $period->save();

        return redirect()->back()->with('success', 'Academic Period activated.');
    }
}

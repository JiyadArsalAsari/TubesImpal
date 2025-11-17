<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        // Ensure only mahasiswa can access this dashboard
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/'); // Redirect non-mahasiswa users
        }

        // Get the authenticated mahasiswa data
        $mahasiswa = Auth::user()->mahasiswa;

        // Return the dashboard view
        return view('mahasiswa.dashboard', compact('mahasiswa'));
    }
}
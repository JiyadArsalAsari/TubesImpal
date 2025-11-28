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

    public function content()
    {
        // Ensure only mahasiswa can access this content
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/'); // Redirect non-mahasiswa users
        }

        // Empty content array - will be populated by admin or AI in the future
        $contents = [];

        // Return the content dashboard view
        return view('mahasiswa.content_dashboard', compact('contents'));
    }

    public function contentDetail($id)
    {
        // Ensure only mahasiswa can access this content
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/'); // Redirect non-mahasiswa users
        }

        // Sample content data (this would typically come from a database)
        $contents = [
            [
                'title' => 'Introduction to Programming',
                'description' => 'Learn the basics of programming with Python',
                'type' => 'video',
                'details' => 'This comprehensive course covers variables, loops, functions, and object-oriented programming concepts.'
            ],
            [
                'title' => 'Web Development Fundamentals',
                'description' => 'Understanding HTML, CSS, and JavaScript',
                'type' => 'article',
                'details' => 'Learn how to create responsive websites using modern web technologies and best practices.'
            ],
            [
                'title' => 'Database Design',
                'description' => 'Learn how to design efficient databases',
                'type' => 'video',
                'details' => 'Master database normalization, indexing, and query optimization techniques.'
            ]
        ];

        // Check if content exists
        if (!isset($contents[$id])) {
            abort(404); // Return 404 if content not found
        }

        $content = $contents[$id];

        // Return the content detail view
        return view('mahasiswa.content_detail', compact('content', 'id'));
    }
}
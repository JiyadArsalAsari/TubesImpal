<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exercise;
use App\Models\DosenMahasiswaRequest;
use App\Models\Mahasiswa;
use App\Models\User;

class DebugExercises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-exercises {mahasiswa_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug exercises in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mahasiswaId = $this->argument('mahasiswa_id');
        
        $exercises = Exercise::all();
        
        $this->info('Total exercises: ' . $exercises->count());
        
        foreach($exercises as $exercise) {
            $this->line('ID: ' . $exercise->id . ', Type: ' . $exercise->type . ', Title: ' . $exercise->title . ', Status: ' . $exercise->status);
            $this->line('  Mahasiswa ID: ' . $exercise->mahasiswa_id . ', Dosen ID: ' . $exercise->dosen_id . ', Deadline: ' . ($exercise->deadline ? $exercise->deadline->format('Y-m-d H:i:s') : 'null'));
        }
        
        $quizCount = Exercise::where('type', 'quiz')->count();
        $assignmentCount = Exercise::where('type', 'assignment')->count();
        
        $this->info('Total quizzes: ' . $quizCount);
        $this->info('Total assignments: ' . $assignmentCount);
        
        // Check connections
        $requests = DosenMahasiswaRequest::all();
        $this->info('\nTotal connection requests: ' . $requests->count());
        foreach($requests as $request) {
            $this->line('Request ID: ' . $request->id . ', Dosen ID: ' . $request->dosen_id . ', Mahasiswa ID: ' . $request->mahasiswa_id . ', Status: ' . $request->status);
        }
        
        // Check users
        $users = \App\Models\User::all();
        $this->info('\nUsers:');
        foreach($users as $user) {
            $this->line('ID: ' . $user->id . ', Name: ' . $user->name . ', Role: ' . $user->role);
            if ($user->mahasiswa) {
                $this->line('  Mahasiswa ID: ' . $user->mahasiswa->id . ', NIM: ' . $user->mahasiswa->nim);
            }
            if ($user->dosen) {
                $this->line('  Dosen ID: ' . $user->dosen->id . ', NIP: ' . $user->dosen->nip);
            }
        }
        
        // Test mahasiswa exercise retrieval
        if ($mahasiswaId) {
            $this->info('\nTesting exercise retrieval for Mahasiswa ID: ' . $mahasiswaId);
            $mahasiswaExercises = Exercise::where('mahasiswa_id', $mahasiswaId)
                ->orderBy('deadline', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();
                
            $this->info('Exercises found for mahasiswa: ' . $mahasiswaExercises->count());
            foreach($mahasiswaExercises as $exercise) {
                $this->line('ID: ' . $exercise->id . ', Type: ' . $exercise->type . ', Title: ' . $exercise->title . ', Status: ' . $exercise->status);
            }
        }
    }
}

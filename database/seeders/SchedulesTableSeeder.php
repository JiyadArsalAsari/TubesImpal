<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\Mahasiswa;

class SchedulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if there are any mahasiswa records
        $mahasiswa = Mahasiswa::first();
        
        if ($mahasiswa) {
            Schedule::create([
                'mahasiswa_id' => $mahasiswa->id,
                'subject_name' => 'Interaksi Manusia Komputer',
                'day' => 'Monday',
                'time' => '08:10 - 10:00',
                'room' => 'C.102'
            ]);
            
            Schedule::create([
                'mahasiswa_id' => $mahasiswa->id,
                'subject_name' => 'Basis Data',
                'day' => 'Tuesday',
                'time' => '10:30 - 12:30',
                'room' => 'A.301'
            ]);
            
            Schedule::create([
                'mahasiswa_id' => $mahasiswa->id,
                'subject_name' => 'Struktur Data',
                'day' => 'Wednesday',
                'time' => '13:00 - 15:00',
                'room' => 'B.205'
            ]);
        }
    }
}

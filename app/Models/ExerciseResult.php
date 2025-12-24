<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'exercise_id',
        'score',
        'attempted_at',
        'status',
        'attempt_number',
        'feedback',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'attempted_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
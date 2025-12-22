<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'dosen_id',
        'mahasiswa_id',
        'type',
        'title',
        'description',
        'deadline',
        'duration_minutes',
        'link',
        'file_attachment',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'exercise_id');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'exercise_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}


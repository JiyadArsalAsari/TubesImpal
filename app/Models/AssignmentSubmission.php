<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'exercise_id',
        'mahasiswa_id',
        'text_submission',
        'file_submission',
        'submitted_at',
        'grade',
        'feedback',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
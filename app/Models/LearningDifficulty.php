<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningDifficulty extends Model
{
    use HasFactory;

    protected $table = 'learning_difficulties';

    protected $fillable = [
        'mahasiswa_id',
        'title',
        'description',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
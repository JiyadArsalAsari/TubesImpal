<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'mahasiswaID',
        'nama',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function learningDifficulties()
    {
        return $this->hasMany(LearningDifficulty::class);
    }
    
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'mahasiswa_id',
        'subject_name',
        'date',
        'day',
        'time'
    ];
    
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
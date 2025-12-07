<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'mahasiswa_id',
        'subject_name',
        'day',
        'time',
        'room'
    ];
    
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}

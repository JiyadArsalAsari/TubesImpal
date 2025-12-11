<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenMahasiswaRequest extends Model
{
    use HasFactory;

    protected $table = 'dosen_mahasiswa_requests';

    protected $fillable = [
        'dosen_id',
        'mahasiswa_id',
        'mahasiswa_name',
        'mahasiswa_email',
        'status'
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftar extends Model
{
    use HasFactory;

    protected $fillable = [
        'lomba_id',
        'nama_peserta',
        'no_hp',
        'rt',
        'kode_tiket',
        'status_kehadiran',
    ];

    public function lomba()
    {
        return $this->belongsTo(Lomba::class);
    }
}
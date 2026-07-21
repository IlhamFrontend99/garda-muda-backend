<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lomba extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'kategori',
        'tipe_peserta',
        'kuota',
        'tanggal',
        'lokasi',
        'deskripsi',
        'status',
        'juara_1',
        'juara_2',
        'juara_3',
    ];

    public function pendaftars()
    {
        return $this->hasMany(Pendaftar::class);
    }
}
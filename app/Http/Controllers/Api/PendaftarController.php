<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lomba;
use App\Models\Pendaftar;
use Illuminate\Http\Request;

class PendaftarController extends Controller
{
    public function index()
    {
        return response()->json(Pendaftar::with('lomba')->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lomba_id' => 'required|exists:lombas,id',
            'nama_peserta' => 'required|string',
            'no_hp' => 'required|string',
            'rt' => 'required|string',
        ]);

        $lomba = Lomba::findOrFail($validated['lomba_id']);

        if ($lomba->terdaftar >= $lomba->kuota) {
            return response()->json(['message' => 'Kuota pendaftaran sudah penuh!'], 400);
        }

        $pendaftar = Pendaftar::create($validated);
        $lomba->increment('terdaftar');

        return response()->json(['message' => 'Berhasil mendaftar lomba', 'data' => $pendaftar], 201);
    }
}

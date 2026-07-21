<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendaftar;
use App\Models\Lomba;
use Illuminate\Support\Str;

class PendaftarController extends Controller
{
    public function index()
    {
        try {
            $pendaftars = Pendaftar::with('lomba')->latest()->get();
            return response()->json($pendaftars, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'lomba_id' => 'required|exists:lombas,id',
                'nama_peserta' => 'required|string|max:255',
                'no_hp' => 'required|string|max:20',
                'rt' => 'required|string|max:50',
            ]);

            $lomba = Lomba::withCount('pendaftars')->findOrFail($validated['lomba_id']);

            if ($lomba->pendaftars_count >= $lomba->kuota) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, kuota perlombaan ini sudah penuh!'
                ], 422);
            }

            $validated['kode_tiket'] = 'GM81-' . strtoupper(Str::random(5));
            $validated['status_kehadiran'] = 'Belum Hadir';

            $pendaftar = Pendaftar::create($validated);
            $pendaftar->load('lomba');

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran berhasil!',
                'data' => $pendaftar
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendaftar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $pendaftar = Pendaftar::findOrFail($id);
            $validated = $request->validate([
                'nama_peserta' => 'required|string|max:255',
                'no_hp' => 'required|string|max:20',
                'rt' => 'required|string|max:50',
                'status_kehadiran' => 'nullable|string',
            ]);

            $pendaftar->update($validated);
            $pendaftar->load('lomba');

            return response()->json([
                'success' => true,
                'message' => 'Data pendaftar berhasil diperbarui!',
                'data' => $pendaftar
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pendaftar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pendaftar = Pendaftar::findOrFail($id);
            $pendaftar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pendaftar berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pendaftar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function scanCheckin(Request $request)
    {
        try {
            $kode = trim($request->input('kode_tiket'));
            $pendaftar = Pendaftar::with('lomba')->where('kode_tiket', $kode)->first();

            if (!$pendaftar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tiket tidak ditemukan! Pastikan Kode Tiket/QR Benar.'
                ], 404);
            }

            $pendaftar->status_kehadiran = 'Hadir';
            $pendaftar->save();

            return response()->json([
                'success' => true,
                'message' => "Check-In Berhasil! Peserta: {$pendaftar->nama_peserta}",
                'data' => $pendaftar
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan check-in: ' . $e->getMessage()
            ], 500);
        }
    }
}
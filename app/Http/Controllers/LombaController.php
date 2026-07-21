<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lomba;
use Illuminate\Support\Facades\Http;

class LombaController extends Controller
{
    public function index()
    {
        try {
            $lombas = Lomba::withCount('pendaftars')->latest()->get();
            
            $data = $lombas->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'kategori' => $item->kategori ?? 'Umum',
                    'tipe_peserta' => $item->tipe_peserta ?? 'Individu',
                    'kuota' => (int) $item->kuota,
                    'terdaftar' => (int) $item->pendaftars_count,
                    'tanggal' => $item->tanggal ?? '2026-08-17',
                    'lokasi' => $item->lokasi ?? 'Lapangan RT06',
                    'deskripsi' => $item->deskripsi ?? '',
                    'status' => $item->status ?? 'Aktif',
                    'juara_1' => $item->juara_1,
                    'juara_2' => $item->juara_2,
                    'juara_3' => $item->juara_3,
                ];
            });

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'kategori' => 'nullable|string',
                'tipe_peserta' => 'required|string',
                'kuota' => 'required|integer|min:1',
                'tanggal' => 'required|string',
                'lokasi' => 'required|string',
                'deskripsi' => 'nullable|string',
            ]);

            $validated['status'] = 'Aktif';
            $lomba = Lomba::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Lomba berhasil ditambahkan!',
                'data' => $lomba
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan lomba: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $lomba = Lomba::findOrFail($id);
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'kategori' => 'nullable|string',
                'tipe_peserta' => 'required|string',
                'kuota' => 'required|integer|min:1',
                'tanggal' => 'required|string',
                'lokasi' => 'required|string',
                'deskripsi' => 'nullable|string',
                'status' => 'nullable|string',
            ]);

            $lomba->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Perlombaan berhasil diperbarui!',
                'data' => $lomba
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui lomba: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $lomba = Lomba::findOrFail($id);
            $lomba->pendaftars()->delete();
            $lomba->delete();

            return response()->json([
                'success' => true,
                'message' => 'Lomba beserta data pendaftarnya berhasil dihapus!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus lomba: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateJuara(Request $request, $id)
    {
        try {
            $lomba = Lomba::findOrFail($id);
            $lomba->status = $request->input('status', 'Selesai');
            $lomba->juara_1 = $request->input('juara_1');
            $lomba->juara_2 = $request->input('juara_2');
            $lomba->juara_3 = $request->input('juara_3');
            $lomba->save();

            return response()->json([
                'success' => true,
                'message' => 'Hasil juara berhasil diperbarui!',
                'data' => $lomba
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui juara: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateAiDescription(Request $request)
    {
        $namaLomba = $request->input('nama_lomba', 'Lomba Kemerdekaan');
        $apiKey = env('GEMINI_API_KEY');

        if ($apiKey) {
            try {
                $response = Http::timeout(8)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "Buatkan deskripsi singkat 2 kalimat yang seru, menarik, dan bersemangat untuk perlombaan 17 Agustus HUT RI ke-81 bernama '{$namaLomba}' di Karang Taruna Garda Muda RT 06."]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        return response()->json(['deskripsi' => trim($text)]);
                    }
                }
            } catch (\Exception $e) {}
        }

        return response()->json([
            'deskripsi' => "Saksikan dan ikuti kemeriahan perlombaan {$namaLomba} memperingati HUT RI ke-81 bersama Garda Muda RT 06! Tunjukkan semangat sportivitas dan kekompakan warga, daftarkan diri Anda sekarang sebelum kuota habis!"
        ]);
    }
}
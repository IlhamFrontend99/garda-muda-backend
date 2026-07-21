<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lomba;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LombaController extends Controller
{
    public function index()
    {
        return response()->json(Lomba::orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string',
            'kategori' => 'required|string',
            'kuota' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'lokasi' => 'required|string',
            'deskripsi' => 'required|string',
        ]);

        $lomba = Lomba::create($validated);
        return response()->json(['message' => 'Lomba berhasil ditambahkan', 'data' => $lomba], 201);
    }

    public function generateAiDescription(Request $request)
    {
        $request->validate(['nama_lomba' => 'required|string']);
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'deskripsi' => "Lomba {$request->nama_lomba} khas Garda Muda RT06! Menjunjung tinggi sportivitas, kebersamaan warga, dan penuh dengan hadiah menarik."
            ]);
        }

        $prompt = "Buatkan deskripsi singkat (2-3 kalimat) yang seru, penuh semangat kemerdekaan 17 Agustus, dan mengajak warga RT06 untuk mendaftar lomba: " . $request->nama_lomba;

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            $result = $response->json();
            $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? "Lomba {$request->nama_lomba} meriah HUT-RI Garda Muda RT06!";

            return response()->json(['deskripsi' => trim($aiText)]);
        } catch (\Exception $e) {
            return response()->json(['deskripsi' => "Lomba {$request->nama_lomba} meriah HUT-RI Garda Muda RT06!"], 200);
        }
    }
}

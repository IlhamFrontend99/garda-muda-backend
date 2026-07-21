<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function getSettings()
    {
        $defaultSettings = [
            'nama_organisasi' => 'GARDA MUDA RT 06',
            'rw_desa' => 'RW 01 DESA WUNGU',
            'slogan_utama' => "KOBARKAN SEMANGAT\nPERSATUAN & KEMERDEKAAN!",
            'deskripsi_hero' => 'Selamat datang di Portal Resmi Perlombaan 17 Agustus Garda Muda RT 06 / RW 01 Desa Wungu. Mari wujudkan kebersamaan, sportivitas, dan keceriaan seluruh warga!',
            'logo_utama' => 'https://upload.wikimedia.org/wikipedia/commons/9/90/National_emblem_of_Indonesia_Garuda_Pancasila.svg',
            'logo_hut' => '/logo-hutri81.png',
            'logo_garda' => '',
            'showcase_title' => 'Keseruan & Gelora Kemerdekaan',
            'showcase_subtitle' => 'Visualisasi dan dokumentasi momen terbaik dari perlombaan Garda Muda',
            'drive_link' => '',
            'instagram' => '',
            'tiktok' => '',
            'youtube' => '',
            'whatsapp' => '',
            'admin_username' => 'admin',
            'admin_password' => 'admin123',
            'showcase_cards' => [],
            'panitia_group_photo' => '',
            'panitia_list' => []
        ];

        try {
            $settings = Setting::all()->pluck('value_data', 'key_name')->toArray();
            
            foreach ($defaultSettings as $key =>$defaultVal) {
                if (!array_key_exists($key, $settings)) {$settings[$key] = is_array($defaultVal) ? json_encode($defaultVal) :$defaultVal;
                }
            }

            if (isset($settings['showcase_cards']) && is_string($settings['showcase_cards'])) {$decoded = json_decode($settings['showcase_cards'], true);$settings['showcase_cards'] = is_array($decoded) ?$decoded : [];
            }

            if (isset($settings['panitia_list']) && is_string($settings['panitia_list'])) {$decodedPanitia = json_decode($settings['panitia_list'], true);$settings['panitia_list'] = is_array($decodedPanitia) ?$decodedPanitia : [];
            }

            return response()->json(['success' => true, 'data' => $settings], 200);         } catch (\Exception$e) {
            return response()->json(['success' => true, 'data' => $defaultSettings], 200);
        }
    }

    public function saveSettings(Request $request)
    {
        try {
            $inputs =$request->all();

            foreach ($inputs as $key =>$val) {
                $valueToSave = is_array($val) ? json_encode($val) : ($val ?? '');
                Setting::updateOrCreate(
                    ['key_name' => $key],
                    ['value_data' => $valueToSave]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengaturan CMS berhasil disimpan!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        try {
            if ($request->hasFile('file')) {$file = $request->file('file');$destinationPath = public_path('uploads');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9\._-]/', '', $file->getClientOriginalName());$file->move($destinationPath,$filename);

                $url = url('uploads/' .$filename);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil diunggah!',
                    'url' => $url
                ], 200);
            }

            return response()->json(['success' => false, 'message' => 'Tidak ada file yang dipilih!'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload gagal: ' . $e->getMessage()], 500);
        }
    }
}
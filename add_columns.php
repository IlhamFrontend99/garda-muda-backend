<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== MEMULAI UPDATE STRUKTUR TABEL TIDB CLOUD ===\n";

try {
    DB::statement("ALTER TABLE pendaftars ADD COLUMN kode_tiket VARCHAR(255) NULL");
    echo "[OK] Kolom 'kode_tiket' BERHASIL ditambahkan ke pendaftars!\n";
} catch (\Exception $e) {
    echo "[INFO] Status kode_tiket: " . $e->getMessage() . "\n";
}

try {
    DB::statement("ALTER TABLE pendaftars ADD COLUMN status_kehadiran VARCHAR(255) DEFAULT 'Belum Hadir'");
    echo "[OK] Kolom 'status_kehadiran' BERHASIL ditambahkan ke pendaftars!\n";
} catch (\Exception $e) {
    echo "[INFO] Status status_kehadiran: " . $e->getMessage() . "\n";
}

try {
    DB::statement("ALTER TABLE lombas ADD COLUMN status VARCHAR(255) DEFAULT 'Aktif'");
    echo "[OK] Kolom 'status' BERHASIL ditambahkan ke lombas!\n";
} catch (\Exception $e) {}

try {
    DB::statement("ALTER TABLE lombas ADD COLUMN juara_1 VARCHAR(255) NULL");
    DB::statement("ALTER TABLE lombas ADD COLUMN juara_2 VARCHAR(255) NULL");
    DB::statement("ALTER TABLE lombas ADD COLUMN juara_3 VARCHAR(255) NULL");
    echo "[OK] Kolom juara_1, juara_2, juara_3 BERHASIL ditambahkan ke lombas!\n";
} catch (\Exception $e) {}

echo "=== EKSKUSI DATABASE SELESAI ===\n";
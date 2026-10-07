<?php
// OPSIONAL 4
require __DIR__ . '/../includes/koneksi.php';

$anggotaFile = __DIR__ . '/../../Jobsheet6/data/anggota.json';
$bukuFile = __DIR__ . '/../../Jobsheet6/data/buku.json';

if (!is_file($anggotaFile) || !is_file($bukuFile)) {
    die("File JSON Jobsheet 6 tidak ditemukan. Pastikan folder Jobsheet6 berada satu level dengan Jobsheet8.\n");
}

$anggota = json_decode(file_get_contents($anggotaFile), true);
$buku = json_decode(file_get_contents($bukuFile), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Format JSON tidak valid: " . json_last_error_msg() . "\n");
}

try {
    $pdo->beginTransaction();

    $stmtAnggota = $pdo->prepare(
        "INSERT INTO anggota (no_anggota, nama, alamat, no_hp)
         VALUES (:no_anggota, :nama, :alamat, :no_hp)"
    );

    foreach ($anggota as $data) {
        $stmtAnggota->execute([
            'no_anggota' => $data['no_anggota'],
            'nama' => $data['nama'],
            'alamat' => $data['alamat'] ?? null,
            'no_hp' => $data['no_hp'] ?? null,
        ]);
    }

    $stmtBuku = $pdo->prepare(
        "INSERT INTO buku (judul, pengarang, tahun, stok, kategori)
         VALUES (:judul, :pengarang, :tahun, :stok, :kategori)"
    );

    foreach ($buku as $data) {
        $stmtBuku->execute([
            'judul' => $data['judul'],
            'pengarang' => $data['pengarang'],
            'tahun' => (int) $data['tahun'],
            'stok' => (int) $data['stok'],
            'kategori' => $data['kategori'] ?? null,
        ]);
    }

    $pdo->commit();

    echo "Migrasi berhasil.\n";
    echo "Anggota: " . count($anggota) . " data\n";
    echo "Buku: " . count($buku) . " data\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Migrasi gagal: " . $e->getMessage() . "\n");
}

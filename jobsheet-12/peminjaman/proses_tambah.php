<?php
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

csrf_verify();

$anggotaId = $_POST['anggota_id'] ?? '';
$bukuId = $_POST['buku_id'] ?? '';

if ($anggotaId === '' || $bukuId === '') {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Anggota dan buku wajib dipilih.'];
    header('Location: tambah.php');
    exit;
}

// NOMOR 1: Validasi bisnis — anggota dengan peminjaman aktif yang sudah
// NOMOR 1: terlambat lebih dari 14 hari tidak boleh meminjam buku baru.
// NOMOR 1: Dicek sebelum transaction & FOR UPDATE, supaya penolakan ini
// NOMOR 1: tidak ikut mengunci baris buku secara percuma.
$cekTerlambat = $pdo->prepare(
    "SELECT id, tanggal_pinjam, CURRENT_DATE - tanggal_pinjam AS hari_terlambat
     FROM peminjaman
     WHERE anggota_id = :anggota_id
       AND status = 'dipinjam'
       AND CURRENT_DATE - tanggal_pinjam > 14
     ORDER BY tanggal_pinjam ASC
     LIMIT 1"
); // NOMOR 1
$cekTerlambat->execute(['anggota_id' => $anggotaId]);
$terlambat = $cekTerlambat->fetch(PDO::FETCH_ASSOC); 

// NOMOR 1: kalau ada peminjaman terlambat, tolak sebelum sempat INSERT
if ($terlambat) {
    $hari = (int) $terlambat['hari_terlambat']; 
    $_SESSION['flash'] = [
        'type' => 'error',
        'pesan' => "Anggota ini punya peminjaman yang sudah terlambat $hari hari (lebih dari 14 hari). Kembalikan dulu buku yang terlambat sebelum meminjam buku baru.", // NOMOR 1
    ]; 
    header('Location: tambah.php'); 
    exit; 
}

try {
    $pdo->beginTransaction();

    // Kunci baris buku (FOR UPDATE) agar stok tidak berubah oleh transaksi lain
    // di tengah proses ini — mencegah stok menjadi negatif akibat race condition.
    $cek = $pdo->prepare("SELECT stok FROM buku WHERE id = :id FOR UPDATE");
    $cek->execute(['id' => $bukuId]);
    $buku = $cek->fetch(PDO::FETCH_ASSOC);

    if (!$buku || $buku['stok'] < 1) {
        throw new Exception('Stok buku tidak tersedia.');
    }

    $insert = $pdo->prepare(
        "INSERT INTO peminjaman (buku_id, anggota_id, tanggal_pinjam, status)
         VALUES (:buku_id, :anggota_id, CURRENT_DATE, 'dipinjam')"
    );
    $insert->execute(['buku_id' => $bukuId, 'anggota_id' => $anggotaId]);

    $update = $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id = :id");
    $update->execute(['id' => $bukuId]);

    $pdo->commit();

    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Peminjaman berhasil dicatat.'];
    header('Location: ../index.php');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Gagal mencatat peminjaman: ' . $e->getMessage()];
    header('Location: tambah.php');
    exit;
}
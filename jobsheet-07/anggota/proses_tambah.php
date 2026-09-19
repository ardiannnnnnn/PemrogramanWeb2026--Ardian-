<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$nama = trim($_POST['nama'] ?? '');
$noAnggota = trim($_POST['no_anggota'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$noHp = trim($_POST['no_hp'] ?? '');

$errors = [];
if ($nama === '') {
    $errors[] = "Nama wajib diisi.";
}
if ($noAnggota === '') {
    $errors[] = "No. Anggota wajib diisi.";
}

// NOMOR 2: Tambahan Validasi Anggota
if ($noAnggota !== '' && !preg_match('/^[A-Za-z0-9-]+$/', $noAnggota)) {
    $errors[] = "No. Anggota hanya boleh berisi huruf, angka, dan tanda hubung (-).";
}

if ($nama !== '' && strlen($nama) < 3) {
    $errors[] = "Nama anggota minimal 3 karakter.";
}

if ($noHp !== '' && !preg_match('/^[0-9]{10,15}$/', $noHp)) {
    $errors[] = "No. HP harus berupa angka antara 10 hingga 15 digit.";
}

// NOMOR 2: Tambahan Validasi Anggota
if (mb_strlen($nama) > 100) {
    $errors[] = "Nama anggota maksimal 100 karakter.";
}
if (mb_strlen($alamat) > 255) {
    $errors[] = "Alamat maksimal 255 karakter.";
}

if (!empty($errors)) {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => implode(' ', $errors)];
    header('Location: tambah.php');
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO anggota (nama, no_anggota, alamat, no_hp)
         VALUES (:nama, :no_anggota, :alamat, :no_hp)
         RETURNING id"
    );
    $stmt->execute([
        'nama' => $nama,
        'no_anggota' => $noAnggota,
        'alamat' => $alamat,
        'no_hp' => $noHp,
    ]);
} catch (PDOException $e) {
    if ($e->getCode() === '23505') {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'No. Anggota sudah dipakai, gunakan nomor lain.'];
        header('Location: tambah.php');
        exit;
    }
    // NOMOR 2: Tambahan Validasi Anggota
    if ($e->getCode() === '22001') {
        $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'Ada isian yang terlalu panjang, persingkat lalu coba lagi.'];
        header('Location: tambah.php');
        exit;
    }
    throw $e;
}

$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anggota berhasil ditambahkan.'];
header('Location: list.php');
exit;
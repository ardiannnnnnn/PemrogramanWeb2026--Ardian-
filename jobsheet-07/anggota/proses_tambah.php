<?php
session_start();
//no 1
// 1. Ambil data dari form
$nama  = trim($_POST['nama'] ?? '');
$nim   = trim($_POST['nim'] ?? '');
$email = trim($_POST['email'] ?? '');
$nohp  = trim($_POST['nohp'] ?? '');

$errors = [];

// 2. Tambahan Validasi Server-Side
if ($nama === '') {
    $errors[] = "Nama anggota wajib diisi.";
}

if ($nim === '') {
    $errors[] = "NIM/ID Anggota wajib diisi.";
} elseif (!is_numeric($nim)) {
    $errors[] = "NIM/ID Anggota hanya boleh berisi angka.";
}

if ($email === '') {
    $errors[] = "Email wajib diisi.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Format email tidak valid.";
}

if ($nohp === '') {
    $errors[] = "Nomor HP wajib diisi.";
} elseif (!is_numeric($nohp)) {
    $errors[] = "Nomor HP hanya boleh berisi angka.";
} elseif (strlen($nohp) < 10 || strlen($nohp) > 13) {
    $errors[] = "Nomor HP harus di antara 10 - 13 digit.";
}

// 3. Tambahan Flash Message saat Error
if (!empty($errors)) {
    $_SESSION['flash'] = [
        'type' => 'error', 
        'pesan' => implode(' ', $errors)
    ];
    header('Location: tambah.php');
    exit;
}

// 4. Proses Simpan Data (Kode Lama)
if (!isset($_SESSION['anggota'])) {
    $_SESSION['anggota'] = [];
}

$_SESSION['anggota'][] = [
    'nama'  => $nama,
    'nim'   => $nim,
    'email' => $email,
    'nohp'  => $nohp,
];

// 5. Tambahan Flash Message Sukses
$_SESSION['flash'] = [
    'type' => 'success', 
    'pesan' => 'Anggota berhasil ditambahkan.'
];

header('Location: list.php');
exit;
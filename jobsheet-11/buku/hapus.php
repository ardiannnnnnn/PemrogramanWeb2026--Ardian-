<?php
require_once __DIR__ . '/../includes/koneksi.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus hanya boleh lewat POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Metode tidak diizinkan.');
}

// Verifikasi token CSRF
csrf_verify(); // SESUAIKAN: samakan dengan nama fungsi di includes/csrf.php

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM buku WHERE id = :id");
    $stmt->execute(['id' => $id]);

    $_SESSION['flash'] = $stmt->rowCount() > 0
        ? ['type' => 'success', 'pesan' => 'Buku berhasil dihapus.'] // SESUAIKAN: nilai type dengan kelas flash di CSS
        : ['type' => 'error', 'pesan' => 'Buku tidak ditemukan.'];   // SESUAIKAN
} else {
    $_SESSION['flash'] = ['type' => 'error', 'pesan' => 'ID buku tidak valid.']; // SESUAIKAN
}

header('Location: list.php');
exit;
<?php
session_start();

// Path koneksi disesuaikan dengan folder includes/koneksi.php
require_once __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_anggota = trim($_POST['no_anggota'] ?? '');
    $nama       = trim($_POST['nama'] ?? '');

    if (empty($no_anggota) || empty($nama)) {
        $_SESSION['flash'] = [
            'type'    => 'danger',
            'message' => 'Semua kolom wajib diisi!'
        ];
        header('Location: tambah.php');
        exit;
    }

    try {
        $sql  = "INSERT INTO anggota (no_anggota, nama) VALUES (:no_anggota, :nama)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':no_anggota' => $no_anggota,
            ':nama'       => $nama
        ]);

        $_SESSION['flash'] = [
            'type'    => 'success',
            'message' => 'Anggota berhasil ditambahkan.'
        ];
        header('Location: list.php');
        exit;

    } catch (PDOException $e) {
        // Menangkap error 23505 (Unique Violation)
        if ($e->getCode() === '23505') {
            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'No. Anggota sudah dipakai, gunakan nomor lain.'
            ];
        } else {
            $_SESSION['flash'] = [
                'type'    => 'danger',
                'message' => 'Terjadi kesalahan pada database: ' . $e->getMessage()
            ];
        }
        header('Location: tambah.php');
        exit;
    }
}
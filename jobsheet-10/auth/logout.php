<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// NOMOR 2: Hentikan "Ingat Saya" saat logout
if (!empty($_SESSION['user_id'])) {
    require_once __DIR__ . '/../includes/koneksi.php';
    $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = :id")
        ->execute(['id' => $_SESSION['user_id']]);
}
setcookie('ingat_saya', '', ['expires' => time() - 3600, 'path' => '/']);
session_destroy();
header('Location: login.php');
exit;

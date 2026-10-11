<?php
// Guard clause: di-include di baris paling atas setiap halaman yang
// membutuhkan login (sebelum header.php mengeluarkan output apa pun),
// agar header('Location: ...') masih bisa dipanggil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// NOMOR 2: Pulihkan login dari cookie "Ingat Saya"
if (empty($_SESSION['user_id']) && !empty($_COOKIE['ingat_saya'])) {
    require_once __DIR__ . '/koneksi.php';
    [$idCookie, $tokenCookie] = array_pad(explode(':', $_COOKIE['ingat_saya'], 2), 2, '');

    if (ctype_digit($idCookie) && $tokenCookie !== '') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $idCookie]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($u && $u['remember_token'] && hash_equals($u['remember_token'], hash('sha256', $tokenCookie))) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['nama']    = $u['nama'];
            $_SESSION['role']    = $u['role'];
        }
    }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}